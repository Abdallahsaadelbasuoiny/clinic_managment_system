<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('clinics', 'services')) {
            Schema::table('clinics', function (Blueprint $table) {
                $table->json('services')->nullable();
            });
        }

        $services = json_encode([
            ['name' => 'عيادة الباطنة العامة والتشخيص', 'description' => 'متابعة الأمراض المزمنة، السكري، وضغط الدم عبر استشاريين متخصصين وفحوصات دورية شاملة.', 'price' => null],
            ['name' => 'عيادة القلب والأوعية الدموية', 'description' => 'تخطيط ورسم القلب، الفحص بالموجات الصوتية، ومتابعة كفاءة عضلة القلب والشرايين.', 'price' => null],
            ['name' => 'عيادة طب وجراحة الأطفال', 'description' => 'متابعة النمو وحديثي الولادة، برامج التطعيمات، وعلاج الحالات الطارئة للأطفال.', 'price' => null],
            ['name' => 'عيادة جراحة العظام والمفاصل', 'description' => 'تشخيص وعلاج آلام المفاصل والعمود الفقري، متابعة الكسور، والتأهيل الحركي.', 'price' => null],
            ['name' => 'طب وجراحة الفم والأسنان', 'description' => 'علاج الجذور واللثة، الحشوات التجميلية، وجراحة الفم الصغرى.', 'price' => null],
            ['name' => 'قسم الحالات المستعجلة', 'description' => 'مسار فوري مخصص للحالات الحرجة والمستعجلة لتخطي قوائم الانتظار.', 'price' => null],
        ], JSON_UNESCAPED_UNICODE);

        DB::table('clinics')->whereNull('services')->update(['services' => $services]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('clinics', 'services')) {
            Schema::table('clinics', function (Blueprint $table) {
                $table->dropColumn('services');
            });
        }
    }
};
