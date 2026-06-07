<?php

namespace App\Livewire\Admin\Products;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImages;
use App\Models\ProductVariant;
use App\Models\ProductVariantImages;
use App\Models\ProductVariantValue;
use App\Models\Variant;
use App\Models\Vendor;
use App\Services\Product\ProductImageService;
use App\Services\Product\ProductVariantService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ProductsCreate extends Component
{
    use WithFileUploads, AuthorizesRequests;

    // Product fields
    public $category_id;
    public $brand_id;
    public $product_name;
    public $product_slug;
    public $product_price;
    public $product_discount;
    public $product_weight;
    public $thumbnail_image;
    public $product_images = [];
    public $short_description;
    public $long_description;
    public $stock = 0;
    public $stock_status = 'in_stock';
    public $is_featured = false;
    public $order_by = 0;
    public $meta_title;
    public $meta_keywords;
    public $meta_description;
    public $status = true;
    
    // Sale Price Fields
    public $sale_price;
    public $sale_start_date;
    public $sale_end_date;
    
    // 👇 NEW: Product Type for Glasses
    public $product_type = 'normal'; // 'normal' or 'sunglasses'
    
    // Listen for file clearing events
    protected $listeners = ['clearFile'];
    
    // Prevent Livewire from resetting file inputs on updates
    protected $rules = [
        'thumbnail_image' => 'nullable|image|max:1024',
        'product_images.*' => 'nullable|image|max:1024',
    ];
    
    // Variant Selection System
    public $selectedVariants = [];
    public $selectedVariantValues = [];
    public $generatedCombinations = [];
    
    // Data for dropdowns
    public $categories = [];
    public $brands = [];
    public $availableVariants;
    public $availableAttributes;
    public $productAttributes = [];
    
    public function mount()
    {
        $this->authorize('create', Product::class);

        $this->categories = Category::where('status', 1)->get();
        $this->brands = Brand::where('status', 1)->get();
        $this->availableVariants = Variant::where('status', 1)->with('variantValues')->get();
        $this->availableAttributes = \App\Models\Attribute::where('status', 1)
            ->with('attributeValue')
            ->get();

        // UNIQUE KEY ke saath initialize
        $this->selectedVariants = [Str::random(8) => ''];
        $this->selectedVariantValues = [];
        $this->generatedCombinations = [];
        $this->productAttributes = [['attribute_id' => '', 'value_id' => '']];
    }

    /**
     *  Check if selected category is Glasses
     */
    public function isGlassesCategory()
    {
        if (!$this->category_id) {
            return false;
        }
        
        // Find Glasses category - case insensitive
        $glassesCategory = Category::whereRaw('LOWER(category_name) = ?', ['glasses'])->first();
        
        if (!$glassesCategory) {
            return false;
            
        }
        
        $category = Category::find($this->category_id);
        
        // Check if it's glasses category or its child
        while ($category) {
            if ($category->id == $glassesCategory->id) {
                return true;
            }
            $category = $category->parent;
        }
        
        return false;
    }

    public function addVariantSelection()
    {
        $this->selectedVariants[Str::random(8)] = '';
    }

    public function updatedSelectedVariants($value, $name)
    {
        if (!empty($value)) {
            if (!isset($this->selectedVariantValues[$value])) {
                $this->selectedVariantValues[$value] = [];
            }
        }
        $this->generateCombinations();
    }

    public function removeVariantSelection($key)
    {
        $variantId = $this->selectedVariants[$key] ?? null;
        unset($this->selectedVariants[$key]);

        if ($variantId && isset($this->selectedVariantValues[$variantId])) {
            unset($this->selectedVariantValues[$variantId]);
        }

        $this->generateCombinations();
    }

    public function updatedSelectedVariantValues($value, $key)
    {
        $this->generateCombinations();
    }

    public function generateCombinations()
    {
        $validSelections = [];

        foreach ($this->selectedVariantValues as $variantId => $valueIds) {
            if (!is_numeric($variantId) || $variantId <= 0) {
                continue;
            }

            $variant = $this->availableVariants->firstWhere('id', (int) $variantId);
            if (!$variant) {
                continue;
            }

            if (!is_array($valueIds)) {
                $valueIds = $valueIds ? [$valueIds] : [];
            }

            $cleanValueIds = array_filter(
                array_map('intval', $valueIds),
                fn($v) => $v > 0
            );

            if (!empty($cleanValueIds)) {
                $validSelections[(int) $variantId] = array_values($cleanValueIds);
            }
        }

        if (empty($validSelections)) {
            $this->generatedCombinations = [];
            return;
        }

        $combinations = $this->cartesianProduct($validSelections);
        $newCombinations = [];

        foreach ($combinations as $idx => $combination) {
            $label = [];
            $slug = [];
            $isValid = true;

            foreach ($combination as $variantId => $valueId) {
                $variant = $this->availableVariants->firstWhere('id', (int) $variantId);
                if (!$variant) {
                    $isValid = false;
                    break;
                }

                $value = $variant->variantValues->firstWhere('id', (int) $valueId);
                if (!$value) {
                    $isValid = false;
                    break;
                }

                $label[] = "{$variant->name}: {$value->value}";
                $slug[] = Str::slug($value->value);
            }

            if (!$isValid || empty($label)) {
                continue;
            }

            $keyParts = [];
            foreach ($combination as $vId => $valId) {
                $keyParts[] = "{$vId}_{$valId}";
            }
            $combinationKey = implode('-', $keyParts);

            $existingData = collect($this->generatedCombinations)
                ->firstWhere('key', $combinationKey);

            $newCombinations[] = [
                'key' => $combinationKey,
                'combination' => $combination,
                'label' => implode(' | ', $label),
                'slug' => implode('-', $slug),
                'sku' => $existingData['sku'] ?? '',
                'barcode' => $existingData['barcode'] ?? '',
                'price' => $existingData['price'] ?? '',
                'sale_price' => $existingData['sale_price'] ?? null,
                'stock' => $existingData['stock'] ?? 0,
                'weight' => $existingData['weight'] ?? '',
                'status' => $existingData['status'] ?? true,
                'images' => $existingData['images'] ?? [],
            ];
        }

        $this->generatedCombinations = $newCombinations;
    }

    private function cartesianProduct($arrays)
    {
        if (empty($arrays)) {
            return [[]];
        }

        $result = [[]];

        foreach ($arrays as $variantId => $valueIds) {
            $temp = [];
            foreach ($result as $resultItem) {
                foreach ($valueIds as $valueId) {
                    $newItem = $resultItem;
                    $newItem[$variantId] = $valueId;
                    $temp[] = $newItem;
                }
            }
            $result = $temp;
        }

        return $result;
    }

    public function removeImage($key)
    {
        unset($this->product_images[$key]);
        $this->product_images = array_values($this->product_images);
    }

    public function addAttributeRow()
    {
        $this->productAttributes[] = ['attribute_id' => '', 'value_id' => ''];
    }

    public function removeAttributeRow($index)
    {
        if ($index > 0) {
            unset($this->productAttributes[$index]);
            $this->productAttributes = array_values($this->productAttributes);
        }
    }

    public function updatedProductAttributes($value, $key)
    {
        if (strpos($key, '.attribute_id') !== false) {
            $index = explode('.', $key)[0];
            $this->productAttributes[$index]['value_id'] = '';
        }
    }

    public function removeCombinationImage($combinationIndex, $imageIndex)
    {
        if (isset($this->generatedCombinations[$combinationIndex]['images'][$imageIndex])) {
            unset($this->generatedCombinations[$combinationIndex]['images'][$imageIndex]);
            $this->generatedCombinations[$combinationIndex]['images'] =
                array_values($this->generatedCombinations[$combinationIndex]['images']);
        }
    }

    private function applySalePriceLogic()
    {
        if ($this->sale_price && !$this->sale_start_date && !$this->sale_end_date) {
            $this->sale_start_date = now()->format('Y-m-d');
            $this->sale_end_date = null;
        }
    }

    private function cleanVariantData()
    {
        $cleanCombinations = [];
        
        foreach ($this->generatedCombinations as $combination) {
            $salePrice = null;
            if (isset($combination['sale_price']) && $combination['sale_price'] !== '' && $combination['sale_price'] !== null) {
                $salePrice = floatval($combination['sale_price']);
            }
            
            $cleanCombinations[] = [
                'key' => $combination['key'] ?? '',
                'combination' => $combination['combination'] ?? [],
                'label' => $combination['label'] ?? '',
                'slug' => $combination['slug'] ?? '',
                'sku' => $combination['sku'] ?? '',
                'barcode' => $combination['barcode'] ?? '',
                'price' => floatval($combination['price'] ?? 0),
                'sale_price' => $salePrice,
                'stock' => intval($combination['stock'] ?? 0),
                'weight' => $combination['weight'] ?? '',
                'status' => $combination['status'] ?? true,
                'images' => $combination['images'] ?? [],
            ];
        }
        
        return $cleanCombinations;
    }

    public function store(ProductImageService $imageService, ProductVariantService $variantService)
    {
        $this->authorize('create', Product::class);

        $vendorId = auth()->user()->hasRole('Vendor')
            ? auth()->user()->vendorId()
            : admin_vendor_id();

        $this->applySalePriceLogic();

        try {
            // Build validation rules dynamically
            $rules = [
                'category_id' => 'nullable|exists:categories,id',
                'brand_id' => 'nullable|exists:brands,id',
                'product_name' => 'required|max:255',
                'product_slug' => [
                    'required',
                    'max:255',
                    Rule::unique('products', 'product_slug')
                        ->where(fn($q) => $q->where('vendor_id', $vendorId)->whereNull('deleted_at'))
                ],
                'product_price' => 'required|numeric|min:0',
                'product_discount' => 'nullable|numeric|min:0|max:100',
                'product_weight' => 'nullable|numeric|min:0',
                'sale_price' => 'nullable|numeric|min:0',
                'sale_start_date' => 'nullable|date',
                'sale_end_date' => 'nullable|date|after_or_equal:sale_start_date',
                'thumbnail_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
                'product_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
                'stock' => 'required|integer|min:0',
                'stock_status' => 'nullable|in:in_stock,out_of_stock,pre_order',
                'status' => 'required|in:0,1',
            ];

            // 👇 Add product_type validation for glasses category
            if ($this->isGlassesCategory()) {
                $rules['product_type'] = 'required|in:normal,sunglasses';
            }

            // Variant validation
            if (!empty($this->generatedCombinations)) {
                $rules['generatedCombinations.*.sku'] = 'required|distinct|max:100';
                $rules['generatedCombinations.*.price'] = 'required|numeric|min:0';
                $rules['generatedCombinations.*.stock'] = 'required|integer|min:0';
                $rules['generatedCombinations.*.images.*'] = 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120';
            }

            $this->validate($rules);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorMessages = [];
            foreach ($e->errors() as $field => $messages) {
                $errorMessages[] = implode(', ', $messages);
            }
            $this->dispatch('show-toast', type: 'error', message: 'Validation Error: ' . implode(', ', $errorMessages));
            return;
        } catch (\Exception $e) {
            $this->dispatch('show-toast', type: 'error', message: 'Error: ' . $e->getMessage());
            return;
        }

        DB::beginTransaction();

        try {
            // Handle thumbnail using service
            $thumbnailPath = null;
            if ($this->thumbnail_image) {
                $thumbnailPath = $imageService->storeThumbnail($this->thumbnail_image);
            }

            // Create product with product_type
            $productData = [
                'category_id' => $this->category_id,
                'brand_id' => $this->brand_id,
                'vendor_id' => $vendorId,
                'product_name' => $this->product_name,
                'product_slug' => $this->product_slug ?: Str::slug($this->product_name),
                'product_price' => $this->product_price,
                'product_discount' => $this->product_discount ?: null,
                'product_weight' => $this->product_weight ?: null,
                'sale_price' => $this->sale_price ?: null,
                'sale_start_date' => $this->sale_start_date,
                'sale_end_date' => $this->sale_end_date,
                'thumbnail_image' => $thumbnailPath,
                'short_description' => $this->short_description ?: null,
                'long_description' => $this->long_description ?: null,
                'stock' => $this->stock,
                'stock_status' => $this->stock_status,
                'is_featured' => $this->is_featured ? 1 : 0,
                'order_by' => $this->order_by ?: null,
                'meta_title' => $this->meta_title ?: null,
                'meta_keywords' => $this->meta_keywords ?: null,
                'meta_description' => $this->meta_description ?: null,
                'status' => $this->status ? 1 : 0,
            ];

            // 👇 Add product_type only for glasses category
            if ($this->isGlassesCategory()) {
                $productData['product_type'] = $this->product_type;
            }

            $product = Product::create($productData);

            // Gallery images using service (only if NO variants)
            if (empty($this->generatedCombinations) && !empty($this->product_images)) {
                $imageService->storeGalleryImages($product, $this->product_images);
            }

            // Product attributes
            if (!empty($this->productAttributes)) {
                foreach ($this->productAttributes as $attr) {
                    if (!empty($attr['attribute_id']) && !empty($attr['value_id'])) {
                        \App\Models\ProductAttribute::create([
                            'product_id' => $product->id,
                            'attribute_id' => $attr['attribute_id'],
                            'attribute_value_id' => $attr['value_id'],
                        ]);
                    }
                }
            }

            // Handle Variants using service
            if (!empty($this->generatedCombinations)) {
                $cleanCombinations = $this->cleanVariantData();
                $variantService->createVariantsFromCombinations($product, $cleanCombinations);
            }

            DB::commit();

            $this->dispatch('show-toast', type: 'success', message: 'Product Created Successfully!');
            return redirect()->route('admin.product');

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Product create error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            $this->dispatch('show-toast', type: 'error', message: 'Error: ' . $e->getMessage());
        }
    }

    public function getCategoryTreeProperty()
    {
        $categories = Category::all();
        return $this->buildTree($categories);
    }

    private function buildTree($categories, $parentId = null)
    {
        $branch = [];

        foreach ($categories as $category) {
            if ($category->parent_id == $parentId) {
                $children = $this->buildTree($categories, $category->id);
                $category->children = $children;
                $branch[] = $category;
            }
        }

        return $branch;
    }

    public function clearFile($property)
    {
        if ($property === 'thumbnail_image') {
            $this->thumbnail_image = null;
        } elseif ($property === 'product_images') {
            $this->product_images = [];
        }
    }

    public function render()
    {
        return view('livewire.admin.products.products-create', [
            'availableVariants' => $this->availableVariants,
            'availableAttributes' => $this->availableAttributes,
        ]);
    }
}