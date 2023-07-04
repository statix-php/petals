@extends('base')

@section('body')
    
    <p>{{ $name }}</p>
    
    <ul>
        @foreach (range(1,5) as $item)
            <li>{{ $name }} - {{ $item }}</li>
        @endforeach
    </ul>

@endsection