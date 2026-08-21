<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Services\SpreadsheetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SpreadsheetSyncTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful sync from Google Sheets CSV.
     */
    public function test_sync_from_csv_successfully_populates_database(): void
    {
        // Mock a 21-column CSV with 64 rows of ACV & 70 rows of Stock 2026 for Pekanbaru, and 67 rows of ACV & 52 rows of Stock 2026 for Sei Pagar
        $csvContent = "Nama,Target Tantangan,ACV,% ACV,+/-,Target Reguler,% ACV,+/-,LM,VS LM %,VS LM Unit,Ket,Target Reguler 2026,Act YTD,+/-,% ACV,Target Perbulan,Stock 2024,Stock 2025,Stock 2026,Total\n";
        
        // Generate Pekanbaru rows: ACV=64, Stock 2026=70 (we need 70 rows total)
        for ($i = 0; $i < 70; $i++) {
            $tantangan = ($i === 0) ? 100 : 0;
            $reguler = ($i === 0) ? 90 : 0;
            $lm = ($i === 0) ? 74 : 0;
            $reguler2026 = ($i === 0) ? 1000 : 0;
            $ytd = ($i === 0) ? 463 : 0;
            $perbulan = ($i === 0) ? 107 : 0;
            
            $acv = ($i < 64) ? 1 : 0;
            $stock2026 = ($i < 70) ? 1 : 0;
            
            $csvContent .= "Pekanbaru,$tantangan,$acv,0%,0,$reguler,0%,0,$lm,0%,0,TURUN,$reguler2026,$ytd,0,0%,$perbulan,0,0,$stock2026,0\n";
        }
        
        // Generate Sei Pagar rows: ACV=67, Stock 2026=52 (we need 67 rows total)
        for ($i = 0; $i < 67; $i++) {
            $tantangan = ($i === 0) ? 75 : 0;
            $reguler = ($i === 0) ? 70 : 0;
            $lm = ($i === 0) ? 61 : 0;
            $reguler2026 = ($i === 0) ? 785 : 0;
            $ytd = ($i === 0) ? 426 : 0;
            $perbulan = ($i === 0) ? 72 : 0;
            
            $acv = ($i < 67) ? 1 : 0;
            $stock2026 = ($i < 52) ? 1 : 0;
            
            $csvContent .= "Sei Pagar,$tantangan,$acv,0%,0,$reguler,0%,0,$lm,0%,0,NAIK,$reguler2026,$ytd,0,0%,$perbulan,0,0,$stock2026,0\n";
        }

        Http::fake([
            'https://example.com/sheet.csv' => Http::response($csvContent, 200)
        ]);

        $service = new SpreadsheetService();
        $result = $service->syncFromCsv('https://example.com/sheet.csv');

        $this->assertTrue($result);
        
        $currentMonth = \Carbon\Carbon::now()->month;
        $remainingMonths = 12 - $currentMonth + 1;
        $expectedPkuPerbulan = $remainingMonths > 0 ? (int)round((1000 - 463) / $remainingMonths) : 0;
        $expectedSeiPerbulan = $remainingMonths > 0 ? (int)round((785 - 426) / $remainingMonths) : 0;

        $this->assertDatabaseHas('cabangs', [
            'nama' => 'Pekanbaru',
            'target_tantangan' => 100,
            'acv' => 64,
            'target_reguler' => 90,
            'lm' => 74,
            'target_reguler_2026' => 1000,
            'act_ytd_jan_2026' => 463,
            'target_perbulan_utk_2026' => $expectedPkuPerbulan,
            'stock_2024' => 0,
            'stock_2025' => 0,
            'stock_2026' => 70,
        ]);

        $this->assertDatabaseHas('cabangs', [
            'nama' => 'Sei Pagar',
            'target_tantangan' => 75,
            'acv' => 67,
            'target_reguler' => 70,
            'lm' => 61,
            'target_reguler_2026' => 785,
            'act_ytd_jan_2026' => 426,
            'target_perbulan_utk_2026' => $expectedSeiPerbulan,
            'stock_2024' => 0,
            'stock_2025' => 0,
            'stock_2026' => 52,
        ]);
    }

    /**
     * Test successful sync from individual cabang CSV URLs.
     */
    public function test_sync_all_from_individual_csv_urls_successfully_populates_database(): void
    {
        // Set up two cabangs with individual urls and target_reguler_2026 configured
        $pekanbaru = Cabang::create([
            'nama' => 'Pekanbaru',
            'target_tantangan' => 100,
            'target_reguler' => 90,
            'target_reguler_2026' => 1000,
            'spreadsheet_url' => 'https://example.com/pekanbaru.csv'
        ]);

        $seipagar = Cabang::create([
            'nama' => 'Sei Pagar',
            'target_tantangan' => 75,
            'target_reguler' => 70,
            'target_reguler_2026' => 785,
            'spreadsheet_url' => 'https://example.com/seipagar.csv'
        ]);

        // Generate Pekanbaru rows: ACV=64, Stock 2026=70 (we need 70 rows total)
        $csvPekanbaru = "Nama,Target Tantangan,ACV,% ACV,+/-,Target Reguler,% ACV,+/-,LM,VS LM %,VS LM Unit,Ket,Target Reguler 2026,Act YTD,+/-,% ACV,Target Perbulan,Stock 2024,Stock 2025,Stock 2026,Total\n";
        for ($i = 0; $i < 70; $i++) {
            $lm = ($i === 0) ? 74 : 0;
            $ytd = ($i === 0) ? 463 : 0;
            $perbulan = ($i === 0) ? 107 : 0;
            
            $acv = ($i < 64) ? 1 : 0;
            $stock2026 = ($i < 70) ? 1 : 0;
            
            $csvPekanbaru .= "Pekanbaru,0,$acv,0%,0,0,0%,0,$lm,0%,0,TURUN,0,$ytd,0,0%,$perbulan,0,0,$stock2026,0\n";
        }

        // Generate Sei Pagar rows: ACV=67, Stock 2026=52 (we need 67 rows total)
        $csvSeiPagar = "Nama,Target Tantangan,ACV,% ACV,+/-,Target Reguler,% ACV,+/-,LM,VS LM %,VS LM Unit,Ket,Target Reguler 2026,Act YTD,+/-,% ACV,Target Perbulan,Stock 2024,Stock 2025,Stock 2026,Total\n";
        for ($i = 0; $i < 67; $i++) {
            $lm = ($i === 0) ? 61 : 0;
            $ytd = ($i === 0) ? 426 : 0;
            $perbulan = ($i === 0) ? 72 : 0;
            
            $acv = ($i < 67) ? 1 : 0;
            $stock2026 = ($i < 52) ? 1 : 0;
            
            $csvSeiPagar .= "Sei Pagar,0,$acv,0%,0,0,0%,0,$lm,0%,0,NAIK,0,$ytd,0,0%,$perbulan,0,0,$stock2026,0\n";
        }

        Http::fake([
            'https://example.com/pekanbaru.csv' => Http::response($csvPekanbaru, 200),
            'https://example.com/seipagar.csv' => Http::response($csvSeiPagar, 200),
        ]);

        $service = new SpreadsheetService();
        $result = $service->syncAll();

        $this->assertTrue($result);
        
        $currentMonth = \Carbon\Carbon::now()->month;
        $remainingMonths = 12 - $currentMonth + 1;
        $expectedPkuPerbulan = $remainingMonths > 0 ? (int)round((1000 - 463) / $remainingMonths) : 0;
        $expectedSeiPerbulan = $remainingMonths > 0 ? (int)round((785 - 426) / $remainingMonths) : 0;

        $this->assertDatabaseHas('cabangs', [
            'id' => $pekanbaru->id,
            'nama' => 'Pekanbaru',
            'target_tantangan' => 100,
            'acv' => 64,
            'target_reguler' => 90,
            'lm' => 74,
            'target_reguler_2026' => 1000,
            'act_ytd_jan_2026' => 463,
            'target_perbulan_utk_2026' => $expectedPkuPerbulan,
            'stock_2024' => 0,
            'stock_2025' => 0,
            'stock_2026' => 70,
        ]);

        $this->assertDatabaseHas('cabangs', [
            'id' => $seipagar->id,
            'nama' => 'Sei Pagar',
            'target_tantangan' => 75,
            'acv' => 67,
            'target_reguler' => 70,
            'lm' => 61,
            'target_reguler_2026' => 785,
            'act_ytd_jan_2026' => 426,
            'target_perbulan_utk_2026' => $expectedSeiPerbulan,
            'stock_2024' => 0,
            'stock_2025' => 0,
            'stock_2026' => 52,
        ]);
    }

    /**
     * Test successful sync when lm_url is configured.
     */
    public function test_sync_with_specific_lm_url_successfully_populates_lm(): void
    {
        $cabang = Cabang::create([
            'nama' => 'Pekanbaru',
            'target_tantangan' => 100,
            'target_reguler' => 90,
            'target_reguler_2026' => 1000,
            'spreadsheet_url' => 'https://example.com/pekanbaru.csv',
            'lm_url' => 'https://example.com/pekanbaru-lm.csv',
        ]);

        // Mock current month: 3 rows (ACV = 3)
        $csvCurrent = "Nama,Target Tantangan,ACV,% ACV,+/-,Target Reguler,% ACV,+/-,LM,VS LM %,VS LM Unit,Ket,Target Reguler 2026,Act YTD,+/-,% ACV,Target Perbulan,Stock 2024,Stock 2025,Stock 2026,Total\n";
        for ($i = 0; $i < 3; $i++) {
            $csvCurrent .= "Pekanbaru,0,1,0%,0,0,0%,0,0,0%,0,TURUN,0,0,0,0%,0,0,0,0,0\n";
        }

        // Mock last month (LM): 7 rows (LM = 7)
        $csvLm = "Nama,Target Tantangan,ACV,% ACV,+/-,Target Reguler,% ACV,+/-,LM,VS LM %,VS LM Unit,Ket,Target Reguler 2026,Act YTD,+/-,% ACV,Target Perbulan,Stock 2024,Stock 2025,Stock 2026,Total\n";
        for ($i = 0; $i < 7; $i++) {
            $csvLm .= "Pekanbaru,0,1,0%,0,0,0%,0,0,0%,0,TURUN,0,0,0,0%,0,0,0,0,0\n";
        }

        Http::fake([
            'https://example.com/pekanbaru.csv' => Http::response($csvCurrent, 200),
            'https://example.com/pekanbaru-lm.csv' => Http::response($csvLm, 200),
        ]);

        $service = new SpreadsheetService();
        $result = $service->syncAll();

        $this->assertTrue($result);
        $this->assertDatabaseHas('cabangs', [
            'id' => $cabang->id,
            'acv' => 3,
            'lm' => 7,
        ]);
    }

    /**
     * Test sync on a branch with empty spreadsheet URL resets metrics.
     */
    public function test_sync_with_empty_url_resets_metrics(): void
    {
        $cabang = Cabang::create([
            'nama' => 'Pekanbaru',
            'target_tantangan' => 100,
            'target_reguler' => 90,
            'target_reguler_2026' => 1000,
            'target_perbulan_utk_2026' => 1000,
            'acv' => 50,
            'lm' => 45,
            'act_ytd_jan_2026' => 200,
            'stock_2024' => 10,
            'stock_2025' => 15,
            'stock_2026' => 20,
            'spreadsheet_url' => null,
        ]);

        $service = new SpreadsheetService();
        $result = $service->syncCabang($cabang);

        $this->assertTrue($result);
        $this->assertDatabaseHas('cabangs', [
            'id' => $cabang->id,
            'acv' => 0,
            'lm' => 0,
            'act_ytd_jan_2026' => 0,
            'target_perbulan_utk_2026' => 1000,
            'stock_2024' => 0,
            'stock_2025' => 0,
            'stock_2026' => 0,
        ]);
    }

    /**
     * Test updating YTD Act via AJAX endpoint.
     */
    public function test_update_ytd_act_endpoint(): void
    {
        $cabang = Cabang::create([
            'nama' => 'Pekanbaru',
            'target_tantangan' => 100,
            'target_reguler' => 90,
            'target_reguler_2026' => 1000,
            'target_perbulan_utk_2026' => 1000,
            'acv' => 50,
            'lm' => 45,
            'act_ytd_jan_2026' => 200,
            'stock_2024' => 0,
            'stock_2025' => 0,
            'stock_2026' => 0,
        ]);

        $user = \App\Models\User::factory()->create([
            'role' => 'editor',
            'allowed_menus' => ['dashboard', 'stu_unit', 'cabang', 'users']
        ]);
        $response = $this->actingAs($user)->post(route('cabang.updateYtd', $cabang), [
            'act_ytd_jan_2026' => 350
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'act_ytd_jan_2026' => 350
        ]);

        $this->assertDatabaseHas('cabangs', [
            'id' => $cabang->id,
            'act_ytd_jan_2026' => 350
        ]);
    }

    /**
     * Test updating monthly sales via AJAX.
     */
    public function test_update_monthly_sales_endpoint(): void
    {
        $cabang = Cabang::create([
            'nama' => 'Pekanbaru',
            'target_tantangan' => 100,
            'target_reguler' => 90,
            'target_reguler_2026' => 1000,
        ]);

        $user = \App\Models\User::factory()->create([
            'role' => 'editor',
            'allowed_menus' => ['cabang']
        ]);

        $monthlySales = [52, 70, 70, 66, 60, 81, 86, 10, 20, 30, 40, 50];

        $response = $this->actingAs($user)->post(route('cabang.updateMonthlySales', $cabang), [
            'monthly_sales' => $monthlySales
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'total' => array_sum($monthlySales),
        ]);

        $cabang->refresh();
        $this->assertEquals($monthlySales, $cabang->getMonthlySalesData());
    }

    /**
     * Test viewer role cannot update monthly sales.
     */
    public function test_viewer_cannot_update_monthly_sales(): void
    {
        $cabang = Cabang::create([
            'nama' => 'Pekanbaru',
            'target_tantangan' => 100,
            'target_reguler' => 90,
            'target_reguler_2026' => 1000,
        ]);

        $viewer = \App\Models\User::factory()->create([
            'role' => 'viewer',
            'allowed_menus' => ['cabang']
        ]);

        $response = $this->actingAs($viewer)->post(route('cabang.updateMonthlySales', $cabang), [
            'monthly_sales' => [10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10]
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test non-super-admin user cannot view super admin accounts in user list.
     */
    public function test_non_super_admin_cannot_see_super_admin_users(): void
    {
        $superAdmin = \App\Models\User::factory()->create([
            'name' => 'Boss Admin',
            'email' => 'boss@aspacindo.com',
            'role' => 'super_admin',
            'allowed_menus' => ['dashboard', 'users']
        ]);

        $editorUser = \App\Models\User::factory()->create([
            'name' => 'Editor Guy',
            'email' => 'editor@aspacindo.com',
            'role' => 'editor',
            'allowed_menus' => ['dashboard', 'users']
        ]);

        $response = $this->actingAs($editorUser)->get(route('users.index'));
        $response->assertStatus(200);
        $response->assertDontSee('boss@aspacindo.com');
        $response->assertSee('Editor Guy');
    }

}
