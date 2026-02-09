@extends('layouts.app')

@section('title', 'Contacts')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Contacts</h1>
        <a href="{{ route('contacts.create') }}" class="btn btn-primary">Add Contact</a>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Contact</th>
                <th>Email</th>
                <th width="200">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($contacts as $contact)
                <tr>
                    <td>
                        <a href="{{ route('contacts.show', $contact) }}">
                            {{ $contact->name }}
                        </a>
                    </td>
                    <td>{{ $contact->contact }}</td>
                    <td>{{ $contact->email }}</td>
                    <td>
                        <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('contacts.destroy', $contact) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No contacts found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection