<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SliderRequest;
use App\Models\Slider;
use App\Traits\HasDeleteAll;

class SliderController extends Controller
{
    use HasDeleteAll;

    protected function deleteAllModel(): string
    {
        return Slider::class;
    }

    protected function deleteAllFileColumns(): array
    {
        return ['image'];
    }
    public function index()
    {
        $sliders = Slider::query()
            ->orderBy('sort_order')
            ->paginate(10)
            ->withQueryString();

        return view('admin.sliders.index', compact('sliders'));
    }

    public function create()
    {
        return view('admin.sliders.form');
    }

    public function store(SliderRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['image'] = upload_file($request->file('image'), 'sliders');

        Slider::create($data);

        return redirect()->route('admin.sliders.index')->with('success', 'Slider berhasil ditambahkan.');
    }

    public function edit(Slider $slider)
    {
        return view('admin.sliders.form', compact('slider'));
    }

    public function update(SliderRequest $request, Slider $slider)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            delete_file($slider->image);
            $data['image'] = upload_file($request->file('image'), 'sliders');
        } else {
            unset($data['image']);
        }

        $data['is_active'] = $request->boolean('is_active');
        $slider->update($data);

        return redirect()->route('admin.sliders.index')->with('success', 'Slider berhasil diperbarui.');
    }

    public function destroy(Slider $slider)
    {
        delete_file($slider->image);
        $slider->delete();

        return redirect()->route('admin.sliders.index')->with('success', 'Slider berhasil dihapus.');
    }
}
