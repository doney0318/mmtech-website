<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SettingController extends Controller
{
    public function index()
    {
        $valueColumn = $this->valueColumn();
        $settings = DB::table('mm_config')->whereIn('key', [
            'site_title',
            'site_description',
            'contact_email',
            'contact_phone',
            'company_address',
        ])->get()->keyBy('key');

        return view('admin.settings.index', compact('settings', 'valueColumn'));
    }

    public function update(Request $request)
    {
        $valueColumn = $this->valueColumn();
        $data = $request->validate([
            'site_title' => ['nullable', 'string'],
            'site_description' => ['nullable', 'string'],
            'contact_email' => ['nullable', 'string'],
            'contact_phone' => ['nullable', 'string'],
            'company_address' => ['nullable', 'string'],
        ]);

        foreach ($data as $key => $value) {
            $exists = DB::table('mm_config')->where('key', $key)->exists();

            if ($exists) {
                DB::table('mm_config')->where('key', $key)->update([
                    $valueColumn => $value,
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('mm_config')->insert([
                    'key' => $key,
                    $valueColumn => $value,
                    'group' => 'base',
                    'type' => 'text',
                    'title_zh' => $key,
                    'title_en' => $key,
                    'sort' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return back()->with('success', '系统设置保存成功');
    }

    private function valueColumn(): string
    {
        return Schema::hasColumn('mm_config', 'value') ? 'value' : 'value_zh';
    }
}
