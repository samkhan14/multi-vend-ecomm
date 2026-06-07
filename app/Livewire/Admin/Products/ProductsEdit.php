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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Carbon\Carbon;

class ProductsEdit extends Component
{
    use WithFileUploads;

    // Product ID
    public $productId;
    public $product;
    
    // Product fields
    public $category_id;
    public $brand_id;
    public $vendor_id;
    public $product_name;
    public $product_slug;
    public $product_price;
    public $product_discount;
    public $product_weight;
    public $thumbnail_image;
    public $existing_thumbnail;
    public $product_images = [];
    public $existing_images = [];
    public $short_description;
    public $long_description;
    public $stock;
    public $stock_status = 'in_stock';
    public $is_featured = false;
    public $order_by = 0;
    public $meta_title;
    public $meta_keywords;
    public $meta_description;
    public $status;

    // Sale Price Fields
    public $sale_price;
    public $sale_start_date;
    public $sale_end_date;

    // 👇 NEW: Product Type for Glasses
    public $product_type = 'normal'; // 'normal' or 'sunglasses'

    // Variant Selection System
    public $selectedVariants = [];
    public $selectedVariantValues = [];
    public $generatedCombinations = [];
    public $existingVariants = [];
    
    // Data for dropdowns
    public $categories = [];
    public $brands = [];
    public $availableVariants;
    public $availableAttributes;
    public $productAttributes = [];

    public function mount($id, $slug)
    {
        $query = Product::with([
            'images',
            'productVariants.variantValues.variant',
            'productVariants.variantValues.variantValue',
            'productVariants.images',
            'productAttributes.attribute',
            'productAttributes.attributeValue',
        ])->where('id', $id);

        if (auth()->user()->vendorId()) {
            $query->where('vendor_id', auth()->user()->vendorId());
        }

        $this->product = $query->firstOrFail();

        if ($this->product->product_slug !== $slug) {
            abort(404);
        }

        $this->authorize('update', $this->product);

        $this->productId = $this->product->id;

        $this->selectedVariants = [];
        $this->selectedVariantValues = [];

        $this->categories = Category::where('status', 1)->whereNull('deleted_at')->get();
        $this->brands = Brand::where('status', 1)->whereNull('deleted_at')->get();
        $this->availableVariants = Variant::where('status', 1)->with('variantValues')->get();
        $this->availableAttributes = \App\Models\Attribute::where('status', 1)
            ->with('attributeValue')
            ->get();

        $this->category_id = $this->product->category_id;
        $this->brand_id = $this->product->brand_id; // 👈 NULL ho sakta hai - perfectly fine
        $this->product_name = $this->product->product_name;
        $this->product_slug = $this->product->product_slug;
        $this->product_price = $this->product->product_price;
        $this->stock = $this->product->stock;
        $this->product_discount = $this->product->product_discount;
        $this->product_weight = $this->product->product_weight;

        // 👇 Load product_type
        $this->product_type = $this->product->product_type ?? 'normal';

        $this->sale_price = $this->product->sale_price;
        $this->sale_start_date = $this->product->sale_start_date 
            ? Carbon::parse($this->product->sale_start_date)->format('Y-m-d\TH:i') : null;
        $this->sale_end_date = $this->product->sale_end_date 
            ? Carbon::parse($this->product->sale_end_date)->format('Y-m-d\TH:i') : null;

        $this->existing_thumbnail = $this->product->thumbnail_image;
        $this->short_description = $this->product->short_description;
        $this->long_description = $this->product->long_description;
        $this->stock_status = $this->product->stock_status;
        $this->is_featured = (bool) $this->product->is_featured;
        $this->order_by = $this->product->order_by;
        $this->meta_title = $this->product->meta_title;
        $this->meta_keywords = $this->product->meta_keywords;
        $this->meta_description = $this->product->meta_description;
        $this->status = (bool) $this->product->status;

        $this->existing_images = $this->product->images->toArray();

        $this->loadExistingAttributes();
        $this->loadExistingVariants();
    }

    /**
     * 👇 Check if selected category is Glasses
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

    private function loadExistingAttributes()
    {
        if ($this->product->productAttributes->count() > 0) {
            $this->productAttributes = [];
            foreach ($this->product->productAttributes as $attr) {
                $this->productAttributes[] = [
                    'id' => $attr->id,
                    'attribute_id' => $attr->attribute_id,
                    'value_id' => $attr->attribute_value_id,
                ];
            }
        } else {
            $this->productAttributes = [['attribute_id' => '', 'value_id' => '']];
        }
    }

    private function loadExistingVariants()
    {
        if ($this->product->productVariants->count() > 0) {
            $this->existingVariants = $this->product->productVariants->toArray();

            $variantMap = [];

            foreach ($this->product->productVariants as $variant) {
                foreach ($variant->VariantValues as $pvv) {
                    $variantId = $pvv->variant_id;
                    $valueId = $pvv->variant_value_id;

                    if (!isset($variantMap[$variantId])) {
                        $variantMap[$variantId] = [];
                    }

                    if (!in_array($valueId, $variantMap[$variantId])) {
                        $variantMap[$variantId][] = $valueId;
                    }
                }
            }

            $this->selectedVariants = array_keys($variantMap);
            $this->selectedVariantValues = $variantMap;
            $this->generatedCombinations = [];

            foreach ($this->product->productVariants as $variant) {
                $combination = [];

                foreach ($variant->VariantValues as $pvv) {
                    $combination[$pvv->variant_id] = $pvv->variant_value_id;
                }

                $label = [];
                $slug = [];
                foreach ($combination as $vId => $valId) {
                    $v = $this->availableVariants->firstWhere('id', $vId);
                    $val = $v ? $v->variantValues->firstWhere('id', $valId) : null;
                    if ($v && $val) {
                        $label[] = "{$v->name}: {$val->value}";
                        $slug[] = Str::slug($val->value);
                    }
                }

                $keyParts = [];
                foreach ($combination as $vId => $valId) {
                    $keyParts[] = "{$vId}_{$valId}";
                }
                $combinationKey = implode('-', $keyParts);

                $existingVariantImages = [];
                foreach ($variant->images as $img) {
                    $existingVariantImages[] = [
                        'id' => $img->id,
                        'path' => $img->image,
                        'is_existing' => true,
                    ];
                }

                $this->generatedCombinations[] = [
                    'variant_id' => $variant->id,
                    'key' => $combinationKey,
                    'combination' => $combination,
                    'label' => implode(' | ', $label),
                    'slug' => implode('-', $slug),
                    'sku' => $variant->sku,
                    'barcode' => $variant->barcode,
                    'price' => $variant->price,
                    'sale_price' => $variant->sale_price,
                    'stock' => $variant->stock,
                    'weight' => $variant->weight,
                    'status' => (bool) $variant->status,
                    'images' => [],
                    'existing_images' => $existingVariantImages,
                ];
            }
        } else {
            $this->selectedVariants = [''];
            $this->selectedVariantValues = [];
            $this->generatedCombinations = [];
        }
    }

    public function addVariantSelection()
    {
        $this->selectedVariants[Str::random(8)] = '';
    }

    public function removeVariantSelection($key)
    {
        unset($this->selectedVariants[$key]);
        $this->generateCombinations();
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
                'variant_id' => $existingData['variant_id'] ?? null,
                'key' => $combinationKey,
                'combination' => $combination,
                'label' => implode(' | ', $label),
                'slug' => implode('-', $slug),
                'sku' => $existingData['sku'] ?? '',
                'barcode' => $existingData['barcode'] ?? '',
                'price' => $existingData['price'] ?? '',
                'sale_price' => $existingData['sale_price'] ?? '',
                'stock' => $existingData['stock'] ?? 0,
                'weight' => $existingData['weight'] ?? '',
                'status' => $existingData['status'] ?? true,
                'images' => $existingData['images'] ?? [],
                'existing_images' => $existingData['existing_images'] ?? [],
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

    public function deleteExistingImage($imageId)
    {
        try {
            $image = ProductImages::find($imageId);
            if ($image) {
                if ($image->image && Storage::disk('public')->exists($image->image)) {
                    Storage::disk('public')->delete($image->image);
                }

                $image->delete();

                $this->existing_images = array_filter($this->existing_images, function ($img) use ($imageId) {
                    return $img['id'] != $imageId;
                });
                $this->existing_images = array_values($this->existing_images);

                $this->dispatch('show-toast', type: 'success', message: 'Image deleted successfully!');
            }
        } catch (\Exception $e) {
            Log::error('Delete image error: ' . $e->getMessage());
            $this->dispatch('show-toast', type: 'error', message: 'Error deleting image!');
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

    public function deleteVariantCombination($index)
    {
        try {
            $combination = $this->generatedCombinations[$index] ?? null;

            if (!$combination) {
                $this->dispatch('show-toast', type: 'error', message: 'Variant combination not found!');
                return;
            }

            if (!empty($combination['variant_id'])) {
                $productVariant = \App\Models\ProductVariant::find($combination['variant_id']);

                if ($productVariant) {
                    foreach ($productVariant->images as $image) {
                        if ($image->image && Storage::disk('public')->exists($image->image)) {
                            Storage::disk('public')->delete($image->image);
                        }
                        $image->delete();
                    }

                    $productVariant->delete();

                    $this->dispatch('show-toast', type: 'success', message: 'Product variant deleted successfully!');
                }
            }

            unset($this->generatedCombinations[$index]);
            $this->generatedCombinations = array_values($this->generatedCombinations);

        } catch (\Exception $e) {
            Log::error('Delete variant combination error: ' . $e->getMessage());
            $this->dispatch('show-toast', type: 'error', message: 'Error deleting variant combination!');
        }
    }

    public function deleteExistingVariantImage($combinationIndex, $imageId)
    {
        $image = ProductVariantImages::find($imageId);
        if ($image) {
            Storage::disk('public')->delete($image->image);
            $image->delete();

            if (isset($this->generatedCombinations[$combinationIndex]['existing_images'])) {
                $this->generatedCombinations[$combinationIndex]['existing_images'] = array_filter(
                    $this->generatedCombinations[$combinationIndex]['existing_images'],
                    function ($img) use ($imageId) {
                        return $img['id'] != $imageId;
                    }
                );
                $this->generatedCombinations[$combinationIndex]['existing_images'] =
                    array_values($this->generatedCombinations[$combinationIndex]['existing_images']);
            }

            $this->dispatch('show-toast', type: 'success', message: 'Variant image deleted!');
        }
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

    private function applySalePriceLogic()
    {
        if ($this->sale_price && !$this->sale_start_date && !$this->sale_end_date) {
            $this->sale_start_date = now()->format('Y-m-d');
            $this->sale_end_date = null;
        }
    }

    public function update(ProductImageService $imageService, ProductVariantService $variantService)
    {
        $this->authorize('update', $this->product);
        $vendorId = vendor_or_admin_id();

        $this->applySalePriceLogic();

        // Build validation rules
        $rules = [
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id', // 👈 NULLABLE (same as create)
            'product_name' => 'required|string|max:255',
            'product_slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'product_slug')
                    ->where(fn($q) => $q->where('vendor_id', $vendorId)->whereNull('deleted_at'))
                    ->ignore($this->productId),
            ],
            'product_price' => 'required|numeric|min:0',
            'product_discount' => 'nullable|numeric|min:0|max:100',
            'stock' => 'required|integer|min:0',
            'product_weight' => 'nullable|numeric|min:0',

            'sale_price' => 'nullable|numeric|min:0',
            'sale_start_date' => 'nullable|date',
            'sale_end_date' => 'nullable|date|after_or_equal:sale_start_date',

            'thumbnail_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'product_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'short_description' => 'nullable|string|max:500',
            'long_description' => 'nullable|string',
            'stock_status' => 'nullable|in:in_stock,out_of_stock,pre_order',
            'is_featured' => 'nullable|boolean',
            'order_by' => 'nullable|integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'status' => 'required|in:0,1',
        ];

        // 👇 Add product_type validation for glasses category
        if ($this->isGlassesCategory()) {
            $rules['product_type'] = 'required|in:normal,sunglasses';
        }

        if (!empty($this->generatedCombinations)) {
            $rules['generatedCombinations.*.sku'] = 'required|string|max:100';
            $rules['generatedCombinations.*.price'] = 'required|numeric|min:0';
            $rules['generatedCombinations.*.stock'] = 'required|integer|min:0';
            $rules['generatedCombinations.*.barcode'] = 'nullable|string|max:100';
            $rules['generatedCombinations.*.sale_price'] = 'nullable|numeric|min:0';
            $rules['generatedCombinations.*.weight'] = 'nullable|numeric|min:0';
            $rules['generatedCombinations.*.images.*'] = 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120';
        }

        $this->validate($rules);

        DB::beginTransaction();

        try {
            $thumbnailPath = $this->existing_thumbnail;
            if ($this->thumbnail_image) {
                $thumbnailPath = $imageService->updateThumbnail($this->thumbnail_image, $this->existing_thumbnail);
            }

            // Update product data
            $productData = [
                'category_id' => $this->category_id,
                'brand_id' => $this->brand_id ?? null,
                'product_name' => $this->product_name,
                'product_slug' => $this->product_slug ?: Str::slug($this->product_name),
                'stock' => $this->stock,
                'product_price' => $this->product_price,
                'product_discount' => $this->product_discount ?: null,
                'product_weight' => $this->product_weight ?: null,

                'sale_price' => $this->sale_price ?: null,
                'sale_start_date' => $this->sale_start_date,
                'sale_end_date' => $this->sale_end_date,

                'thumbnail_image' => $thumbnailPath,
                'short_description' => $this->short_description ?: null,
                'long_description' => $this->long_description ?: null,
                'stock' => $this->stock ?? 0,
                'stock_status' => $this->stock_status ?? 'in_stock',
                'is_featured' => $this->is_featured ? 1 : 0,
                'order_by' => $this->order_by ?: null,
                'meta_title' => $this->meta_title ?: null,
                'meta_keywords' => $this->meta_keywords ?: null,
                'meta_description' => $this->meta_description ?: null,
                'status' => $this->status ? 1 : 0,
            ];

            // 👇 Add product_type for glasses category
            if ($this->isGlassesCategory()) {
                $productData['product_type'] = $this->product_type;
            }

            $this->product->update($productData);

            if (!empty($this->product_images)) {
                $imageService->storeGalleryImages($this->product, $this->product_images);
            }

            \App\Models\ProductAttribute::where('product_id', $this->product->id)->delete();

            foreach ($this->productAttributes as $attr) {
                if (!empty($attr['attribute_id']) && !empty($attr['value_id'])) {
                    \App\Models\ProductAttribute::create([
                        'product_id' => $this->product->id,
                        'attribute_id' => $attr['attribute_id'],
                        'attribute_value_id' => $attr['value_id'],
                    ]);
                }
            }

            if (!empty($this->generatedCombinations)) {
                $variantService->updateVariantsFromCombinations(
                    $this->product,
                    $this->generatedCombinations
                );
            }

            DB::commit();

            $this->dispatch('show-toast', type: 'success', message: 'Product Updated Successfully!');

            return redirect()->route('admin.product');
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($thumbnailPath) && $thumbnailPath !== $this->existing_thumbnail) {
                Storage::disk('public')->delete($thumbnailPath);
            }

            Log::error('Product update error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            $this->dispatch('show-toast', type: 'error', message: 'Error: ' . $e->getMessage());
        }
    }

    public function removeProductImage($properties = null)
    {
        return;
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

    public function render()
    {
        return view('livewire.admin.products.products-edit', [
            'availableVariants' => $this->availableVariants,
            'availableAttributes' => $this->availableAttributes,
        ]);
    }
}