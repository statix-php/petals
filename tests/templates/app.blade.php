@extends('base')

@section('body')

    <h1>{{ $title }}</h1>

    <ul>
        <li>Name: {{ $name }}</li>
    </ul>

    @foreach(range(1, 5) as $item)
        <p>{{ $item }}</p>
    @endforeach

    @foreach([1, 2] as $number)
        <p>{{ $number }}</p>
    @endforeach

    {{-- 
        @php $email = 'John@email.com'; @endphp 
    --}}

    {{ '<p class="bg-red-100">this is escaped</p>' }}

    {!! '<p class="bg-red-100">this is unescaped</p>' !!}

    {{-- this is a comment --}}

    <div class="container">
        Hello, @{{ name }}.
    </div>    

@endsection

