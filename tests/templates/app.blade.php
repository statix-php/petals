@extends('layout')

@section('content')

    <p>This is the actual final page</p>

    @if ($name === 'John' && str_contains($name, 'ohn'))
        <p>Hello {{ $name }}</p>
    @endif

    @if($name === 'John')
        <p>Hello {{ $name }}</p>    
    @elseif($name === 'Doe')
        <p>Hola {{ $nickname }}</p>
    @else
        <p>Hello Guest</p>
    @endif

    @include('partial', [
        'value' => '123'
    ])

    @foreach(range(1, 5) as $item)
        <p>{{ $item }}</p>
    @endforeach

    @foreach([1, 2] as $number)
        <p>{{ $number }}</p>
    @endforeach

    @for($i = 0; $i < 10; $i++)
        <p>{{ $i }}</p>
    @endfor

    {{-- 
        @php $email = 'John@email.com'; @endphp 
    --}}

    {{ $email }}

    {{ '<p class="bg-red-100">this is escaped</p>' }}

    {!! '<p class="bg-red-100">this is unescaped</p>' !!}

    {{-- this is a comment --}}

    <div class="container">
        Hello, @{{ name }}.
    </div>    

    @verbatim
        <div class="container">
            Hello, {{ name }}.
        </div>

        <div class="container">
            Hello, {{ jim }}.
        </div>
    @endverbatim

@endsection

