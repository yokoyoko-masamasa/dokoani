<div>
    <h1 class="mt-4 mb-2 text-lg font-bold text-gray-800">アカウントの削除（退会）</h1>

    <p class="text-sm text-gray-800">退会すると、以下のデータがすべて削除され、元に戻すことはできません。</p>

    <ul class="mt-2 list-disc ps-5 text-sm text-gray-800">
        <li>見たいリスト</li>
        <li>視聴済みリスト</li>
        <li>マイサブスク登録情報</li>
    </ul>

    <button
        type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="mt-6 block w-full px-3 py-2 rounded-md text-center text-sm bg-white text-gray-800 border border-gray-300"
    >退会する</button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-gray-800">本当に退会しますか？</h2>

            <p class="mt-1 text-sm text-gray-800">パスワードを入力して、退会を確定してください。</p>

            <div class="mt-6">
                {{-- ラベル・入力欄・エラー表示の部品を読み込む --}}
                <x-input-label for="password" value="パスワード" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="パスワード"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <button type="button" x-on:click="$dispatch('close')" class="px-3 py-2 rounded-md text-center text-sm bg-white text-gray-800 border border-gray-300">キャンセル</button>

                <button type="submit" class="ms-3 px-3 py-2 rounded-md text-center text-sm bg-white text-gray-800 border border-gray-300">退会する</button>
            </div>
        </form>
    </x-modal>
</div>
