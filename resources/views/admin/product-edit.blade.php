<x-app-layout>
    <x-slot name="title">商品編集</x-slot>

    <div class="max-w-2xl mx-auto" style="padding: 3rem 1.5rem;">
        <div style="margin-bottom:2.5rem;">
            <a href="{{ route('admin.products') }}" style="font-size:0.75rem; color:#8A9899; text-decoration:none;">← 商品一覧</a>
            <h1 style="font-family:'Noto Serif JP', serif; font-size:1.5rem; font-weight:300; color:#2E3A3B; margin-top:0.5rem;">
                商品編集：{{ $product->name }}
            </h1>
        </div>

        @if($errors->any())
        <div style="background:#FDF0F0; border:1px solid #c0392b; border-radius:2px; padding:0.875rem 1rem; margin-bottom:1.5rem; font-size:0.82rem; color:#c0392b;">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('admin.products.update', $product) }}"
              style="background:#fff; border:1px solid #E8E4DC; border-radius:4px; padding:2rem;">
            @csrf
            @method('PATCH')

            @foreach([
                ['name' => 'name', 'label' => '商品名', 'type' => 'text', 'required' => true],
                ['name' => 'price', 'label' => '通常価格（円）', 'type' => 'number', 'required' => true],
                ['name' => 'subscription_price', 'label' => '定期価格（円）', 'type' => 'number', 'required' => true],
                ['name' => 'stock', 'label' => '在庫数', 'type' => 'number', 'required' => true],
            ] as $field)
            <div style="margin-bottom:1.25rem;">
                <label style="display:block; font-size:0.75rem; color:#8A9899; margin-bottom:0.4rem;">
                    {{ $field['label'] }}{{ $field['required'] ? ' *' : '' }}
                </label>
                <input type="{{ $field['type'] }}" name="{{ $field['name'] }}"
                       value="{{ old($field['name'], $product->{$field['name']}) }}"
                       {{ $field['required'] ? 'required' : '' }}
                       style="width:100%; padding:0.75rem 1rem; border:1px solid #D8D4CC; border-radius:2px; font-size:0.85rem; box-sizing:border-box; outline:none;"
                       onfocus="this.style.borderColor='#4A5859'" onblur="this.style.borderColor='#D8D4CC'">
            </div>
            @endforeach

            <div style="margin-bottom:1.25rem;">
                <label style="display:block; font-size:0.75rem; color:#8A9899; margin-bottom:0.4rem;">商品説明</label>
                <textarea name="description" rows="4"
                    style="width:100%; padding:0.75rem 1rem; border:1px solid #D8D4CC; border-radius:2px; font-size:0.85rem; box-sizing:border-box; resize:vertical; outline:none;"
                    onfocus="this.style.borderColor='#4A5859'" onblur="this.style.borderColor='#D8D4CC'">{{ old('description', $product->description) }}</textarea>
            </div>

            <div style="margin-bottom:2rem; display:flex; align-items:center; gap:0.75rem;">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                       style="width:16px; height:16px; cursor:pointer;">
                <label for="is_active" style="font-size:0.85rem; color:#2E3A3B; cursor:pointer;">公開する</label>
            </div>

            <div style="display:flex; gap:1rem;">
                <button type="submit"
                    style="background:#4A5859; color:#fff; padding:0.875rem 2rem; font-size:0.82rem; letter-spacing:0.1em; border:none; border-radius:2px; cursor:pointer;">
                    更新する
                </button>
                <a href="{{ route('admin.products') }}"
                   style="padding:0.875rem 1.5rem; font-size:0.82rem; color:#8A9899; text-decoration:none; border:1px solid #D8D4CC; border-radius:2px;">
                    キャンセル
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
