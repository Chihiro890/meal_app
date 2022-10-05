<x-app-layout>
    <div class="container lg:w-3/4 md:w-4/5 w-11/12 mx-auto my-8 px-8 py-4 bg-white shadow-md">

        <x-flash-message :message="session('notice')" />

        <x-validation-errors :errors="$errors" />

        <article class="mb-2">
            <h2 class="font-bold font-sans break-normal text-gray-900 pt-6 pb-1 text-3xl md:text-4xl">{{ $meal->title }}
            </h2>
            <h3>{{ $meal->user->name }}</h3>

            <p class="text-sm mb-2 md:text-base font-normal text-gray-600">
                現在時刻: <span class="text-red-400 font-bold">{{ date('Y-m-d H:i:s') }}</span>
            <div>記事作成日: {{ $meal->created_at }}</div>
            </p>

            {{-- <img src="{{ Storage::url('images/meals/' . $meal->image) }}" alt="" class="mb-4"> --}}
            <img src="{{ $meal->image_url }}" alt="" class="mb-4">
            
            <p class="text-gray-700 text-base">{!! nl2br(e($meal->body)) !!}</p>
        </article>

        @auth
            @if ($like)
                <form action="{{ route('meals.likes.destroy', [$meal->id, $like->id]) }}" method="post">
                    @csrf
                    @method('DELETE')
                    <button class="bg-pink-400 hover:bg-pink-800 text-white font-bold py-2 px-4 rounded">
                        お気に入り削除
                    </button>
                </form>
                <p class="font-black">お気に入り数：{{ $meal->likes->count() }}</p>
            @else
                <form action="{{ route('meals.likes.store', $meal->id) }}" method="post">
                    @csrf
                    <button class="bg-blue-500 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded">
                        お気に入り
                    </button>
                </form>
                <p class="font-black">お気に入り数：{{ $meal->likes->count() }}</p>
        </div>
        @endif
    @endauth

    <div class="flex flex-row text-center my-4">

        @can('update', $meal)
            <a href="{{ route('meals.edit', $meal) }}"
                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-20 mr-2">編集</a>
        @endcan

        @can('delete', $meal)
            <form action="{{ route('meals.destroy', $meal) }}" method="post">
                @csrf
                @method('DELETE')
                <input type="submit" value="削除" onclick="if(!confirm('削除しますか？')){return false};"
                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-20">
            </form>
        @endcan
    </div>
    </div>
</x-app-layout>
