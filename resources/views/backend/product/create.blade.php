@extends('backend.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">Add Product</h5>
    <div class="card-body">
        <form method="post" action="{{route('product.store')}}" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="form-group">
                <label for="inputProductCode" class="col-form-label">Product Code <span
                        class="text-danger">*</span></label>
                <input id="inputProductCode" type="text" name="product_code" placeholder="Enter product code"
                    value="{{old('product_code')}}" class="form-control">
                @error('product_code')
                <span class="text-danger">{{$message}}</span>
                @enderror
                @error('slug')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="inputTitle" class="col-form-label">Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="title" name="title" value="{{old('title')}}">

                @error('title')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="description" class="col-form-label">Description</label>
                <textarea class="form-control" id="description" name="description">{{old('description')}}</textarea>
                @error('description')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="product_features" class="col-form-label">Product Features</label>
                <textarea class="form-control" id="product_features"
                    name="product_features">{{old('product_features')}}</textarea>
                @error('product_features')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="size_chart" class="col-form-label">Size Chart</label>
                <textarea class="form-control" id="size_chart" name="size_chart">{{old('size_chart')}}</textarea>
                @error('size_chart')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="is_featured">Is Featured</label><br>
                <input type="checkbox" name='is_featured' id='is_featured' value='1' checked> Yes
            </div>

            <div class="form-group">
                <label for="cat_id">Category <span class="text-danger">*</span></label>
                <select name="cat_id" id="cat_id" class="form-control">
                    <option value="">--Select any category--</option>
                    @foreach($categories as $key=>$cat_data)
                    <option value='{{$cat_data->id}}'>{{$cat_data->title}}</option>
                    @endforeach
                </select>
                @error('cat_id')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>

            <div class="form-group d-none" id="child_cat_div">
                <label for="child_cat_id">Sub Category</label>
                <select name="child_cat_id" id="child_cat_id" class="form-control">
                    <option value="">--Select any category--</option>
                    {{-- @foreach($parent_cats as $key=>$parent_cat)
                    <option value='{{$parent_cat->id}}'>{{$parent_cat->title}}</option>
                    @endforeach --}}
                </select>
            </div>

            <div class="form-group">
                <label for="discount" class="col-form-label">Discount(%)</label>
                <input id="discount" type="number" name="discount" min="0" max="100" placeholder="Enter discount"
                    value="{{old('discount')}}" class="form-control">
                @error('discount')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="gst_percent" class="col-form-label">GST Rate(%)</label>
                <input id="gst_percent" type="number" name="gst_percent" min="0" max="100"
                    placeholder="Enter GST percent" value="{{old('gst_percent')}}" class="form-control">
                @error('gst_percent')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>


            <div class="form-group">
                <label for="brand_id">Brand</label>
                {{-- {{$brands}} --}}

                <select name="brand_id" class="form-control">
                    <option value="">--Select Brand--</option>
                    @foreach($brands as $brand)
                    <option value="{{$brand->id}}">{{$brand->title}}</option>
                    @endforeach
                </select>
            </div>

            <!--<div class="form-group">-->
            <!--  <label for="condition">Condition</label>-->
            <!--  <select name="condition" class="form-control">-->
            <!--      <option value="">--Select Condition--</option>-->
            <!--      <option value="default">Default</option>-->
            <!--      <option value="new">New</option>-->
            <!--      <option value="hot">Hot</option>-->
            <!--  </select>-->
            <!--</div>-->

            <div class="form-group">
                <label for="hsn_code">HSN Code <span class="text-danger">*</span></label>
                <input id="hsn_code" type="text" name="hsn_code" placeholder="Enter HSN code"
                    value="{{old('hsn_code')}}" class="form-control">
                @error('hsn_code')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="inputPhoto" class="col-form-label">Photo <span class="text-danger">*</span></label>
                <input id="inputFile" type="file" name="photo[]" class="form-control" multiple>
                <div id="holder" style="margin-top:15px;max-height:100px;"></div>
                @error('photo')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="inputVideo" class="col-form-label">
                    Video
                </label>
                <input id="inputVideo" type="file" name="video" class="form-control" multiple accept="video/*">
                <div id="video-holder" style="margin-top:15px;max-height:100px;"></div>
                @error('video')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>

            @include('backend.partials._meta_fields', ['item' => null])

            <div class="form-group">
                <label for="status" class="col-form-label">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-control">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
                @error('status')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>

            {{-- Sizes, prices, colours, images and the quantity grid --}}
            @include('backend.product._form_variants', ['product' => null, 'postSize' => $postSize])

            <div class="form-group mb-3">
                <button type="reset" class="btn btn-warning">Reset</button>
                <button class="btn btn-success" type="submit">Submit</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{asset('public/backend/summernote/summernote.min.css')}}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
@endpush
@push('scripts')
<script src="{{asset('public/backend/summernote/summernote.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>

<script>
    $(document).ready(function () {
        //   $('#title').summernote({
        //     placeholder: "Write short description.....",
        //       tabsize: 2,
        //       height: 100
        //   });
    });

    $(document).ready(function () {
        $('#description,#product_features, #size_chart').summernote({
            placeholder: "Write Details.....",
            tabsize: 2,
            height: 150
        });
    });
</script>

<script>
    $('#cat_id').change(function () {
        var cat_id = $(this).val();
        if (cat_id != null) {
            // Ajax call
            $.ajax({
                url: "{{ url('admin/category') }}/" + cat_id + "/child",
                data: {
                    _token: "{{csrf_token()}}",
                    id: cat_id
                },
                type: "POST",
                success: function (response) {
                    if (typeof (response) != 'object') {
                        response = $.parseJSON(response)
                    }
                    var html_option = "<option value=''>----Select sub category----</option>"
                    if (response.status) {
                        var data = response.data;
                        if (response.data) {
                            $('#child_cat_div').removeClass('d-none');
                            $.each(data, function (id, title) {
                                html_option += "<option value='" + id + "'>" + title + "</option>"
                            });
                        }
                    }
                    else {
                        $('#child_cat_div').addClass('d-none');
                    }
                    $('#child_cat_id').html(html_option);
                }
            });
        }
    })
</script>
@endpush