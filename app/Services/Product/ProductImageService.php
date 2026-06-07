<?php

namespace App\Services\Product;

use App\Models\ProductImages;
use App\Models\ProductVariantImages;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class ProductImageService
{
    /**
     * Store product thumbnail
     */
    public function storeThumbnail(UploadedFile $image): string
    {
        return $this->process(
            $image,
            'products/thumbnails',
            500,
            500,
            80
        );
    }

    /**
     * Update product thumbnail
     */
    public function updateThumbnail(UploadedFile $image, string $existingThumbnail = null): string
    {
        // Delete old thumbnail if exists
        if ($existingThumbnail) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($existingThumbnail);
        }

        return $this->process(
            $image,
            'products/thumbnails',
            500,
            500,    
            80
        );
    }

    /**
     * Store gallery images (no variants case)
     */
    public function storeGalleryImages($product, array $images): void
    {
        foreach ($images as $index => $image) {

            // 🔥 Livewire edge cases guard
            if (! $image || ! method_exists($image, 'getRealPath')) {
                continue;
            }

            $realPath = $image->getRealPath();

            if (! $realPath || ! file_exists($realPath)) {
                continue;
            }

            $path = $this->process(
                $image,
                'products/gallery',
                600,
                600,
                75
            );

            ProductImages::create([
                'product_id' => $product->id,
                'vendor_id'  => auth()->user()->vendorId(),
                'image'      => $path,
                'sort_order' => $index,
            ]);
        }
    }

    /**
     * Store variant images
     */
    public function storeVariantImages($variant, array $images): void
    {
        foreach ($images as $index => $image) {
            $path = $this->process(
                $image,
                'products/variants',
                600,
                600,
                75
            );

            ProductVariantImages::create([
                'product_variant_id' => $variant->id,
                'image'              => $path,
                'sort_order'         => $index,
            ]);
        }
    }

    /**
     * Core image processor (Intervention Image)
     */
   private function process(
    UploadedFile $image,
    string $directory,
    int $maxWidth,
    int $maxHeight,
    int $quality,
    ?int $userId = null
): string {
    $basePath = $this->userBasePath($userId);
    $directory = "{$basePath}/{$directory}";

    $filename = uniqid('', true) . '.webp';
    $fullPath = storage_path("app/public/{$directory}/{$filename}");

    // 🔥 FIX: ensure directory exists
    if (!is_dir(dirname($fullPath))) {
        mkdir(dirname($fullPath), 0755, true);
    }
    $manager = new ImageManager(new Driver());

    $img = $manager->read($image->getRealPath());

    $img->resize($maxWidth, $maxHeight, function ($c) {
        $c->aspectRatio();
        $c->upsize();
    });

    $img->toWebp($quality)->save($fullPath);

    return "{$directory}/{$filename}";
}



    public function storeBanner(UploadedFile $image): string
    {
        return $this->process(
            $image,
            'banners',
            1920,
            600,
            90
        );
    }

    public function storeMobileBanner(UploadedFile $image): string
    {
        return $this->processBanner($image, 1080, 600, 90);
    }

    private function processBanner(
        UploadedFile $image,
        int $maxWidth,
        int $maxHeight,
        int $quality
    ): string {
        $filename = uniqid() . '.webp';
        $path = storage_path("app/public/banners/{$filename}");

        $manager = new ImageManager(new Driver());
        $img = $manager->read($image->getRealPath());

        // resize without crop
        $img->resize(1920, null, function ($c) {
            $c->aspectRatio();
            $c->upsize();
        });

        // 🔥 canvas (height control)
        $img->resizeCanvas(
            1920,
            600,
            'center',
            false,
            '#ffffff' // ya transparent
        );

        $img->toWebp(90)->save($path);

        return "banners/{$filename}";
    }
    
    private function userBasePath(?int $userId = null): string
    {
        $id = $userId ?? auth()->id();
        return "users/user-{$id}";
    }


}
