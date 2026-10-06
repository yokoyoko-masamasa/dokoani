<x-app-layout>
    <section class="px-4 md:px-6 py-8">
        {{-- 設定画面へ戻るリンク --}}
        <a href="{{ route('mypage.settings') }}" class="text-sm text-gray-800">← 設定に戻る</a>

        <h1 class="mt-4 mb-2 text-lg font-bold text-gray-800">サブスクを追加</h1>

        {{-- 選択肢が無ければ、フォームの代わりに案内を出す --}}
        @if ($services->isEmpty())
            <p class="text-gray-800">追加できるサービスがありません</p>
        @else
            <p class="text-sm text-gray-800">契約するサービスと月額料金を入力してください。</p>
            <p class="mt-2 text-sm text-gray-800">月額料金は1円以上の整数で入力してください（0円・小数点は入力できません）</p>

            {{-- 追加フォームはPOSTで送る。@csrf は不正な送信を防ぐ合言葉 --}}
            <form method="POST" action="{{ route('subscriptions.store') }}" class="mt-6">
                @csrf

                <div>
                    {{-- ラベルとエラー表示の部品を読み込む --}}
                    <x-input-label for="streaming_service_id" value="サービス" />
                    <select id="streaming_service_id" name="streaming_service_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">選択してください</option>
                        @foreach ($services as $service)
                            {{-- 入力し直しのとき、前回の選択を残す --}}
                            <option value="{{ $service->id }}" @selected(old('streaming_service_id') == $service->id)>{{ $service->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('streaming_service_id')" class="mt-2" />
                </div>

                <div class="mt-4">
                    {{-- ラベル・入力欄・エラー表示の部品を読み込む --}}
                    <x-input-label for="price" value="月額料金（円）" />
                    <x-text-input id="price" name="price" type="number" min="1" :value="old('price')" class="block mt-1 w-full" />
                    <x-input-error :messages="$errors->get('price')" class="mt-2" />
                </div>

                <button type="submit" class="mt-6 block w-full px-3 py-2 rounded-md text-center text-sm bg-white text-gray-800 border border-gray-300">追加する</button>
            </form>
        @endif
    </section>
</x-app-layout>
