@extends('layouts.app1')

@section('content')

<div class="container">

<h3>Add Medicine</h3>

<form method="POST" action="{{ route('hospital_admin.medicines.store') }}">
@csrf

<div class="row">

<div class="col-md-4">
<label>Medicine Name</label>
<input type="text" name="name" class="form-control" required>
</div>

<div class="col-md-3">
<label>Dosage</label>
<input type="text" name="dosage" class="form-control">
</div>

<div class="col-md-3">
<label>Price</label>
<input type="number" step="0.01" name="price" class="form-control" required>
</div>

<div class="col-md-2">
<label>&nbsp;</label>
<button class="btn btn-primary w-100">Add</button>
</div>

</div>

</form>

<hr>

<h4>Medicines List</h4>

<table class="table table-bordered">

<tr>
<th>Name</th>
<th>Dosage</th>
<th>Price</th>
</tr>

@foreach($medicines as $medicine)

<tr>
<td>{{ $medicine->name }}</td>
<td>{{ $medicine->dosage }}</td>
<td>{{ $medicine->price }}</td>
</tr>

@endforeach

</table>

</div>

@endsection