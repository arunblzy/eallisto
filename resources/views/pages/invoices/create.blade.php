@extends('layouts.admin')
@section('content')
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                 data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                 class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Create Invoice</h1>
            </div>
        </div>
    </div>
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-xxl">
            {{--  =============================================  --}}
            <form id="invoice-create-form" class="form" method="post" action="{{ $storeUrl }}" autocomplete="off">
                @csrf
                <div class="fv-row mb-10">
                    <label class="required form-label fs-6 mb-2">Customer</label>
                    <select id="customer" name="customer" class="form-control form-control-lg form-control-solid">

                    </select>
                </div>
                <div class="fv-row mb-10">
                    <label class="required form-label fs-6 mb-2">Date</label>
                    <input class="form-control form-control-solid" name="date" id="date"/>
                </div>

                <div class="fv-row mb-10">
                    <label class="required form-label fs-6 mb-2">Amount</label>
                    <input class="form-control form-control-lg form-control-solid" type="number" placeholder=""
                           name="amount" id="amount" autocomplete="off" />
                </div>

                <div class="fv-row mb-10">
                    <label class="required form-label fs-6 mb-2">Status</label>
                    <select id="status" name="status" class="form-control form-control-lg form-control-solid">

                    </select>
                </div>

                <button id="invoice-create-form-submit" type="submit" class="btn btn-primary">Submit</button>
                <a href="{{ route('admin.invoices.index') }}" id="invoice-create-form-cancel" class="btn
                btn-outline-secondary">Cancel</a>
            </form>
            {{--  =============================================  --}}
        </div>
    </div>
@endsection
@include('plugins.validation')
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#invoice-create-form').validate({
                rules: {
                    customer: {
                        required: true
                    },
                    date: {
                        required: true,
                    },
                    amount: {
                        required: true,
                    },
                    status: {
                        required: true
                    }
                },
                messages: {
                    customer: "Please select a customer",
                    date: "Please select a date",
                    amount: "Please enter an amount",
                    address: "Please select a status"
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                },
                submitHandler: function(form) {
                    $.ajax({
                        url: $(form).attr('action'),
                        type: 'post',
                        data: $(form).serialize(),
                        success: function(response) {
                            Swal.fire({
                                text: "Invoice created successfully!",
                                icon: "success",
                                buttonsStyling: !1,
                                confirmButtonText: "Ok, got it!",
                                customClass: { confirmButton: "btn btn-primary" }
                            }).then(function (e) {
                                location.href = $('#invoice-create-form-cancel').attr('href');
                            });
                        },
                        error: function(xhr, status, error) {
                            var errors = xhr.responseJSON.errors;
                            if (errors || xhr.status === 422) {
                                $.each(errors, function(key, value) {
                                    let elementById = $('#'+key + '-error');
                                    (elementById.is('*')) ? elementById.remove() : '';
                                    let element = $('[name="' + key + '"]');
                                    element.addClass('error');
                                    element.after('<label id="' + key + '-error" class="error ' +
                                        '" for="' + key + '" >' + value[0] +
                                        '</label>');
                                });
                            } else {
                                let errMsg = statusMessages[xhr.status];
                                Swal.fire({
                                    text: errMsg,
                                    icon: "error",
                                    buttonsStyling: !1,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: { confirmButton: "btn btn-primary" },
                                });
                            }
                        }
                    });
                }
            });

            $("#date").daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    locale: {
                        format: 'DD-MM-YYYY'
                    },
                    minYear: 1901,
                    maxYear: parseInt(moment().format("YYYY"),10)
                }, function(start, end, label) {
                    // var years = moment().diff(start, "years");
                    // alert("You are " + years + " years old!");
                }
            );

            $('#customer').select2({
                ajax: {
                    url: "{{ route('get.select2',['table' => 'customers']) }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            searchTerm: params.term
                        };
                    },
                    processResults: function (response) {
                        return {
                            results: response.data
                        };
                    },
                    cache: true,
                },
                minimumInputLength: 1
            });

            $('#status').select2({
                ajax: {
                    url: "{{ route('get.select2',['table' => 'payment-status']) }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            searchTerm: params.term
                        };
                    },
                    processResults: function (response) {
                        return {
                            results: response.data
                        };
                    },
                    cache: true,
                },
                minimumInputLength: 0
            });
        });
    </script>


@endpush