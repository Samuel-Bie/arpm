
<?php

namespace Tests;

use Tests\TestCase;

use App\Services\SpreadsheetService;

class SpreadsheetServiceTest extends TestCase
{


    // Successfully imports data from a valid spreadsheet file
    public function test_successful_import_from_valid_spreadsheet()
    {
        $filePath = 'products.xlsx';

        // Simulating the import method to return an array of $products_data
        $mockImporter->method('import')->willReturn([
            ['product_code' => 'PR001', 'quantity' => 1],
            ['product_code' => 'PR001', 'quantity' => 3]
        ]);
        app()->instance('importer', $mockImporter);

        $spreadsheetService = new SpreadsheetService();
        $spreadsheetService->processSpreadsheet($filePath);

        $this->assertDatabaseHas(
            'products',
            ['code' => 'PR001', 'quantity' => 1]
        );
        $this->assertDatabaseHas(
            'products',
            ['code' => 'PR002', 'quantity' => 21]
        );

        $this->assertDatabaseHas(
            'products',
            ['code' => 'PR003', 'quantity' => 18]
        );
        $this->assertDatabaseHas(
            'products',
            ['code' => 'PR004', 'quantity' => 14]
        );
    }

    // Handles an empty spreadsheet file gracefully
    public function test_handles_empty_spreadsheet_file()
    {
        $filePath = 'products.empty.xlsx';

        $mockImporter->method('import')->willReturn([]);
        app()->instance('importer', $mockImporter);

        $spreadsheetService = new SpreadsheetService();
        $spreadsheetService->processSpreadsheet($filePath);

        $this->assertDatabaseCount('products', 0);
    }

    // Validates each row with correct product_code and quantity
    public function test_validating_each_row_with_correct_product_code_and_quantity()
    {
        $filePath = 'product.xlsx';


        $mockImporter->method('import')->willReturn([
            ['product_code' => 'PR001', 'quantity' => 1],
            ['product_code' => 'PR002', 'quantity' => 3]
        ]);
        app()->instance('importer', $mockImporter);

        $mockProduct = $this->createMock(Product::class);
        $mockProduct->method('create')->willReturnSelf();
        $this->app->instance(Product::class, $mockProduct);

        $mockValidator = $this->createMock(Validator::class);
        $mockValidator->method('fails')->willReturn(false);
        $mockValidator->method('validated')->willReturn(['product_code' => 'P001', 'quantity' => 10]);
        Validator::shouldReceive('make')->andReturn($mockValidator);

        $spreadsheetService = new SpreadsheetService();
        $spreadsheetService->processSpreadsheet($filePath);

        $this->assertDatabaseHas('products', ['code' => 'PR001', 'quantity' => 10]);
        $this->assertDatabaseMissing('products', ['code' => 'PR002', 'quantity' => 5]);
    }


    // Dispatches ProcessProductImage job for each created product
    public function test_dispatches_process_product_image_for_each_created_product()
    {
        $filePath = 'product.xlsx';


        $mockImporter->method('import')->willReturn([
            ['product_code' => 'PR001', 'quantity' => 1],
            ['product_code' => 'PR002', 'quantity' => 3]
        ]);
        app()->instance('importer', $mockImporter);

        $mockProduct = $this->createMock(Product::class);
        $mockProduct->method('create')->willReturnSelf();

        $mockValidator = $this->createMock(Validator::class);
        $mockValidator->method('fails')->willReturn(false);
        $mockValidator->method('validated')->willReturn(['product_code' => 'P001', 'quantity' => 10]);

        Validator::shouldReceive('make')->andReturn($mockValidator);
        Product::shouldReceive('create')->andReturn($mockProduct);

        ProcessProductImage::shouldReceive('dispatch')->twice();

        $spreadsheetService = new SpreadsheetService();
        $spreadsheetService->processSpreadsheet($filePath);
    }

    // Handles a spreadsheet with multiple valid rows correctly
    public function test_handles_multiple_valid_rows_correctly() {}

    // Skips rows with missing or invalid product_code
    public function test_skips_rows_with_missing_or_invalid_product_code() {}

    // Skips rows with non-integer or negative quantity
    public function test_skips_rows_with_non_integer_or_negative_quantity() {}

    // Deals with duplicate product_code entries in the spreadsheet
    public function test_deals_with_duplicate_product_code_entries() {}

    // Processes a spreadsheet with mixed valid and invalid rows
    public function test_process_mixed_valid_and_invalid_rows() {}

    // Validates the uniqueness of product_code against existing database records
    public function test_validating_product_code_uniqueness() {}

    // Handles large spreadsheet files efficiently
    public function test_handles_large_spreadsheet_files_efficiently()
    {
        // usage of generators
    }

    // Ensures that the ProcessProductImage job is dispatched only for valid products
    public function test_process_spreadsheet_dispatches_job_for_valid_products() {}

    // Logs validation errors for debugging purposes
    public function test_logs_validation_errors_for_debugging() {}

    // Manages database transaction integrity during product creation
    public function test_database_transaction_integrity_product_creation() {}
}
