@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Contact Details</h5>
                    <div>
                        <a href="{{ route('contacts.edit', $contact->id) }}" class="btn btn-primary">Edit</a>
                        <a href="{{ route('contacts.index') }}" class="btn btn-secondary">Back to List</a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-8">
                            <h4>Basic Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Contact ID</th>
                                    <td>{{ $contact->contact_id }}</td>
                                </tr>
                                <tr>
                                    <th>Name</th>
                                    <td>{{ $contact->name }}</td>
                                </tr>
                                <tr>
                                    <th>Nationality</th>
                                    <td>{{ $contact->nationality }}</td>
                                </tr>
                                <tr>
                                    <th>Birthdate</th>
                                    <td>{{ $contact->birthdate ? $contact->birthdate->format('Y-m-d') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Phone Number</th>
                                    <td>{{ $contact->phone_no }}</td>
                                </tr>
                                <tr>
                                    <th>TIN</th>
                                    <td>{{ $contact->tin }}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td>{{ $contact->address }}</td>
                                </tr>
                            </table>

                            <h4 class="mt-4">Passport Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Passport Number</th>
                                    <td>{{ $contact->passport_number }}</td>
                                </tr>
                                <tr>
                                    <th>Place of Issue</th>
                                    <td>{{ $contact->passport_place_of_issue }}</td>
                                </tr>
                                <tr>
                                    <th>Issue Date</th>
                                    <td>{{ $contact->passport_issue_date ? $contact->passport_issue_date->format('Y-m-d') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Expiry Date</th>
                                    <td>{{ $contact->passport_expiry_date ? $contact->passport_expiry_date->format('Y-m-d') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Passport File</th>
                                    <td>
                                        @if($contact->passport_file)
                                            <a href="{{ asset('storage/contacts/documents/' . $contact->passport_file) }}" target="_blank">View File</a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            <h4 class="mt-4">Residence Visa Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Visa Number</th>
                                    <td>{{ $contact->residence_visa_number }}</td>
                                </tr>
                                <tr>
                                    <th>File Number</th>
                                    <td>{{ $contact->residence_visa_file_number }}</td>
                                </tr>
                                <tr>
                                    <th>Issue Date</th>
                                    <td>{{ $contact->residence_visa_issue_date ? $contact->residence_visa_issue_date->format('Y-m-d') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Expiry Date</th>
                                    <td>{{ $contact->residence_visa_expiry_date ? $contact->residence_visa_expiry_date->format('Y-m-d') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Visa File</th>
                                    <td>
                                        @if($contact->residence_visa_file)
                                            <a href="{{ asset('storage/contacts/documents/' . $contact->residence_visa_file) }}" target="_blank">View File</a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            <h4 class="mt-4">Emirates ID Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Emirates ID Number</th>
                                    <td>{{ $contact->emirates_id_number }}</td>
                                </tr>
                                <tr>
                                    <th>Issue Date</th>
                                    <td>{{ $contact->emirates_id_issue_date ? $contact->emirates_id_issue_date->format('Y-m-d') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Expiry Date</th>
                                    <td>{{ $contact->emirates_id_expiry_date ? $contact->emirates_id_expiry_date->format('Y-m-d') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Emirates ID File</th>
                                    <td>
                                        @if($contact->emirates_id_file)
                                            <a href="{{ asset('storage/contacts/documents/' . $contact->emirates_id_file) }}" target="_blank">View File</a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center mb-4">
                                @if($contact->photo)
                                    <img src="{{ asset('storage/contacts/photos/' . $contact->photo) }}" class="img-thumbnail" style="max-width: 200px;">
                                @else
                                    <div class="border p-3 text-center">No photo available</div>
                                @endif
                            </div>

                            <div class="mb-4">
                                <h5>CV / Resume</h5>
                                @if($contact->cv_file)
                                    <a href="{{ asset('storage/contacts/documents/' . $contact->cv_file) }}" class="btn btn-info btn-block" target="_blank">View CV/Resume</a>
                                @else
                                    <div class="border p-3 text-center">No CV/Resume available</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Relationships Section -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Relationships</h5>
                                    <a href="{{ route('contacts.relationships.create', $contact->id) }}" class="btn btn-primary btn-sm">Add Relationship</a>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Relationship</th>
                                                <th>Related To</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Notes</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($relationships as $relationship)
                                                <tr>
                                                    <td>{{ $relationship->name }}</td>
                                                    <td>{{ $relationship->owner_reverse }}</td>
                                                    <td>{{ $relationship->start_date ? $relationship->start_date->format('Y-m-d') : 'N/A' }}</td>
                                                    <td>{{ $relationship->end_date ? $relationship->end_date->format('Y-m-d') : 'N/A' }}</td>
                                                    <td>{{ $relationship->notes }}</td>
                                                    <td>
                                                        <a href="{{ route('contacts.relationships.edit', [$contact->id, $relationship->id]) }}" class="btn btn-sm btn-primary">Edit</a>
                                                        <form action="{{ route('contacts.relationships.destroy', [$contact->id, $relationship->id]) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this relationship?')">Delete</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">No relationships found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reverse Relationships Section -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Reverse Relationships (Where this contact is referenced)</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Owner</th>
                                                <th>Relationship</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($reverseRelationships as $relationship)
                                                <tr>
                                                    <td>{{ $relationship->owner }}</td>
                                                    <td>{{ $relationship->name }}</td>
                                                    <td>{{ $relationship->start_date ? $relationship->start_date->format('Y-m-d') : 'N/A' }}</td>
                                                    <td>{{ $relationship->end_date ? $relationship->end_date->format('Y-m-d') : 'N/A' }}</td>
                                                    <td>{{ $relationship->notes }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">No reverse relationships found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
