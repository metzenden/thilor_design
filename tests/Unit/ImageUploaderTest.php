<?php

namespace Tests\Unit;

use App\Support\ImageUploader;
use App\Support\PlaceholderImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageUploaderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_store_from_contents_resizes_and_converts_to_webp(): void
    {
        $bytes = PlaceholderImage::make('Test', 800, 1000);

        $path = ImageUploader::storeFromContents($bytes, 'unit-test');

        $this->assertStringEndsWith('.webp', $path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_store_accepts_an_uploaded_file(): void
    {
        $bytes = PlaceholderImage::make('Test', 800, 1000);
        $tmp = tempnam(sys_get_temp_dir(), 'img').'.jpg';
        file_put_contents($tmp, $bytes);

        $uploaded = new UploadedFile($tmp, 'test.jpg', 'image/jpeg', null, true);
        $path = ImageUploader::store($uploaded, 'unit-test');

        $this->assertStringEndsWith('.webp', $path);
        Storage::disk('public')->assertExists($path);

        @unlink($tmp);
    }

    public function test_delete_removes_the_stored_file(): void
    {
        $bytes = PlaceholderImage::make('Test', 800, 1000);
        $path = ImageUploader::storeFromContents($bytes, 'unit-test');

        ImageUploader::delete($path);

        Storage::disk('public')->assertMissing($path);
    }
}
