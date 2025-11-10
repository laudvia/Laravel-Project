@extends('layout')
@section('content')
    <h3>
        Contact
    </h3>
    <p>
        Name: {{$contact['name']}}
    </p>
    <p>
        Adress: {{$contact['adress']}}
    </p>
    <p>
        Phone: {{$contact['phone']}}
    </p>
    <p>
        Email: {{$contact['email']}}
    </p>
@endsection
@section('name')
    <h2>Шмелев Андрей Павлович 231-322</h2>
@endsection