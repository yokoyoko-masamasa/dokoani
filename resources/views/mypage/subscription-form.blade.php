<x-app-layout>
    <section class="px-4 md:px-6 py-8">
        {{-- 設定画面へ戻るリンク --}}
        <a href="{{ route('mypage.settings') }}" class="text-sm text-gray-800">← 設定に戻る</a>

        <h1 class="mt-4 mb-2 text-lg font-bold text-gray-800">{{ isset($userSubscription) ? '料金を変更' : 'サブスクを追加' }}</h1>

        {{-- 選択肢が無ければ、フォームの代わりに案内を出す --}}
        @if (! isset($userSubscription) && $services->isEmpty())
            <p class="text-gray-800">追加できるサービスがありません</p>
        @else
            @isset($userSubscription)
                <p class="text-sm text-gray-800">月額料金だけ変更できます。サービスを変えるときは、削除して追加し直してください。</p>
            @else
                <p class="text-sm text-gray-800">契約するサービスと月額料金を入力してください。</p>
            @endisset
            <p class="mt-2 text-sm text-gray-800">月額料金は1円以上の整数で入力してください（0円・小数点は入力できません）</p>

            {{-- 追加フォームはPOSTで送る。@csrf は不正な送信を防ぐ合言葉 --}}
            {{-- 送信先は、変更なら更新、追加なら保存のルートにする --}}
            <form method="POST" action="{{ isset($userSubscription) ? route('subscriptions.update', $userSubscription) : route('subscriptions.store') }}" class="mt-6">
                @csrf
                {{-- 変更のときは、PATCH として送る --}}
                @isset($userSubscription)
                    @method('PATCH')
                @endisset

                {{-- 変更ならサービス名を文字で出し、追加なら選択肢を出す --}}
                @isset($userSubscription)
                    <div>
                        {{-- ラベルの部品を読み込む --}}
                        <x-input-label value="サービス" />
                        <p class="mt-1 text-gray-800">{{ $userSubscription->streamingService->name }}</p>
                    </div>
                @else
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
                @endisset

                <div class="mt-4">
                    {{-- ラベル・入力欄・エラー表示の部品を読み込む --}}
                    <x-input-label for="price" value="月額料金（円）" />
                    <x-text-input id="price" name="price" type="number" min="1" :value="old('price', isset($userSubscription) ? $userSubscription->price : null)" class="block mt-1 w-full" />
                    <x-input-error :messages="$errors->get('price')" class="mt-2" />
                </div>

                <button type="submit" class="mt-6 block w-full px-3 py-2 rounded-md text-center text-sm bg-white text-gray-800 border border-gray-300">{{ isset($userSubscription) ? '変更する' : '追加する' }}</button>
            </form>
        @endif
    </section>
</x-app-layout>
