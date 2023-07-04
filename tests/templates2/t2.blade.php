@extends('base')

@section('body')

<p>this is t2</p>

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

    <p>Unescaped Ouput: {!! $name !!}</p>

    <p>The current time is {{ time() }}</p>

    @if (count($records) === 1)
        I have one record!
    @elseif (count($records) > 1)
        I have multiple records!
    @else
        I don't have any records!
    @endif

    @unless(false)
        You are not signed in.
    @endunless

    @isset($records)
        // $records is defined and is not null...
    @endisset
    
    @empty($records)
        // $records is "empty"...
    @endempty

@endsection

