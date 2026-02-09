@extends('layouts.app')

@section('title', $contact->name)

@section('content')
    <h1>Contact Details</h1>

    <table class="table">
        <tr>
            <th width="150">ID</th>
            <td>{{ $contact->id }}</td>
        </tr>
        <tr>
            <th>Name</th>
            <td>{{ $contact->name }}</td>
        </tr>
        <tr>
            <th>Contact</th>
            <td>{{ $contact->contact }}</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ $contact->email }}</td>
        </tr>
    </table>
    @auth
        <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-warning">Edit</a>
        <form action="{{ route('contacts.destroy', $contact) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
        </form>
    @endauth
    <a href="{{ route('contacts.index') }}" class="btn btn-secondary">Back to List</a>
@endsection