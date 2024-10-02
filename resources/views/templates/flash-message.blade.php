@if($message = Session::get('success'))
    <div class="text-white px-6 py-4 border-0 rounded relative mb-4 bg-green-500">
        <span class="inline-block align-middle mr-8">
            {{ $message }}
        </span>
        <button class="absolute bg-transparent text-2xl font-semibold leading-none right-0 top-0 mt-4 mr-6 outline-none focus:outline-none" onclick="closeAlert(event)">
            <span>×</span>
        </button>
    </div>
@endif

@if($message = Session::get('warning'))
    <div class="text-white px-6 py-4 border-0 rounded relative mb-4 bg-orange-500">
        <span class="inline-block align-middle mr-8">
            {{ $message }}
        </span>
        <button
            class="absolute bg-transparent text-2xl font-semibold leading-none right-0 top-0 mt-4 mr-6 outline-none focus:outline-none"
            onclick="closeAlert(event)">
            <span>×</span>
        </button>
    </div>
@endif

@if($message = Session::get('info'))
    <div class="text-white px-6 py-4 border-0 rounded relative mb-4 bg-blue-500">
        <span class="inline-block align-middle mr-8">
            {{ $message }}
        </span>
        <button class="absolute bg-transparent text-2xl font-semibold leading-none right-0 top-0 mt-4 mr-6 outline-none focus:outline-none" onclick="closeAlert(event)">
            <span>×</span>
        </button>
    </div>
@endif

@if($errors->any())
    <div class="text-white px-6 py-4 border-0 rounded relative mb-4 bg-red-500">
        <ul class="list-none list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button
            class="absolute bg-transparent text-2xl font-semibold leading-none right-0 top-0 mt-4 mr-6 outline-none focus:outline-none"
            onclick="closeAlert(event)">
            <span>×</span>
        </button>
    </div>
@endif

<script>
    function closeAlert(event) {
        let element = event.target;
        while (element.nodeName !== "BUTTON") {
            element = element.parentNode;
        }
        element.parentNode.parentNode.removeChild(element.parentNode);
    }
</script>