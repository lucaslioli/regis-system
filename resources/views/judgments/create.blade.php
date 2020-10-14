@extends('layouts.app')

@section('content')

    <div class="container">
        <form method="POST">
            @csrf
            @method('PUT')

            <div class="row d-flex flex-column">
                <label for="query_title">Query title:</label>
                <div class="card mb-3">
                    <input type="hidden" id="query_id" value="">
                    <div class="card-body">Lorem ipsum dolor sit amet consectetur adipiscing elit</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="query_description">Description:</label>
                <div class="card">
                    <div class="card-body">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum viverra mi ut sapien tempus, nec laoreet leo egestas. Nulla at nulla sit amet lacus lacinia facilisis. Sed semper ex at dui consequat blandit. Donec leo mi, sodales eu lacus vitae, ultrices fringilla massa. Suspendisse pulvinar, nunc ut eleifend sagittis, mi massa ultrices felis, sed dapibus felis lorem a purus.</div>
                </div>
            </div>

            <div class="row progress mt-4 mb-5">
                <div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
            </div>

            <div class="row">

                <div class="form-group col-9">
                    <div class="form-group row">

                        <div class="document-title d-flex justify-content-between">
                            <label>Original document: <a href="#" target="blank">BR-BG.03964.pdf <i class="fas fa-external-link-alt"></i></a></label>
                            <label>25/100</label>
                        </div>
                        <div class="card">
                            <div class="card-body document-text">
                                <mark>Lorem ipsum dolor sit amet, consectetur adipiscing elit</mark>. Vestibulum viverra mi ut sapien tempus, nec laoreet leo egestas. Nulla at nulla sit <mark>amet</mark> lacus lacinia facilisis. Sed semper ex at dui consequat blandit. Donec leo mi, sodales eu lacus vitae, ultrices fringilla massa. Suspendisse pulvinar, nunc ut eleifend sagittis, mi massa ultrices felis, sed dapibus felis <mark>lorem</mark> a purus. Fusce aliquet tristique turpis eget molestie. Donec eget gravida <mark>lorem</mark>.

                                <br> Aliquam erat volutpat. Sed mollis congue sapien id cursus. Proin sodales ut justo vitae molestie. Sed rutrum risus tortor, et suscipit augue ultrices ac. Nunc tempor <mark>elit</mark> et justo maximus iaculis. Cras sed sagittis enim. Aenean interdum eleifend ex, quis pulvinar nulla aliquam vitae. Etiam nec varius libero, in interdum arcu. Duis nec felis massa. Proin sed metus ultricies, ultrices lacus non, bibendum augue. Duis sapien lectus, mollis ut condimentum at, volutpat in risus.

                                <br> In et nulla quis sem mollis consequat pretium mattis neque. Suspendisse vestibulum consequat leo et imperdiet. Nulla augue <mark>elit</mark>, condimentum nec purus et, eleifend vehicula lectus. Donec eget est eu eros condimentum interdum. Cras quis mi id magna mattis semper vel in <mark>dolor</mark>. Quisque lacinia imperdiet dui, condimentum imperdiet nibh bibendum eget. Donec mauris <mark>elit</mark>, egestas ac feugiat porta, vehicula vel libero. Vivamus purus <mark>elit</mark>, faucibus ut placerat vitae, iaculis a mi. Cras dignissim tortor at tortor accumsan convallis. Praesent vel nulla dignissim, ultricies felis in, ultricies est.

                                <br> Morbi eget malesuada urna. Nam commodo sem eget nisi egestas, ultricies aliquet mi semper. Phasellus in nunc a nulla cursus eleifend vitae at purus. Pellentesque vitae nibh <mark>elit</mark>. Vivamus aliquet eleifend lectus. Duis posuere mauris eu tellus mollis, et fringilla nisl elementum. Nulla facilisi. Aenean non neque quis erat porta <mark>consectetur</mark> non vitae diam. Aliquam eu <mark>consectetur</mark> nisl. Donec auctor tempor est, ut ullamcorper diam sollicitudin vel.

                                <br> Vivamus laoreet lobortis ex, sed convallis <mark>lorem</mark> vulputate at. Nam lobortis <mark>dolor</mark> non dapibus aliquam. In tellus mi, dignissim vel libero id, pharetra mattis nisi. Integer sollicitudin sed nunc sit <mark>amet</mark> aliquam. Duis vehicula <mark>elit</mark> eget placerat ornare. Praesent sed feugiat ligula. Proin id tristique nulla, quis molestie nunc.
                            </div>
                        </div>
                    </div>
                </div>
    
                <div class="form-group col-3">
                    <label>With respect to the query, this document is:</label>

                    <div class="judgment">
                        <div class="custom-control custom-switch">
                            {{-- 1.0 --}}
                            <input type="radio" class="custom-control-input" id="very-relevant" name="judgment">
                            <label class="custom-control-label" for="very-relevant">Very Relevant</label> 
                        </div>
                        <div class="custom-control custom-switch">
                            {{-- 0.7 --}}
                            <input type="radio" class="custom-control-input" id="relevant" name="judgment">
                            <label class="custom-control-label" for="relevant">Relevant</label>
                        </div>
                        <div class="custom-control custom-switch">
                            {{-- 0.3 --}}
                            <input type="radio" class="custom-control-input" id="little-relevant" name="judgment">
                            <label class="custom-control-label" for="little-relevant">Marginally Relevant</label>
                        </div>
                        <div class="custom-control custom-switch">
                            {{-- 0.0 --}}
                            <input type="radio" class="custom-control-input" id="not-relevant" name="judgment">
                            <label class="custom-control-label" for="not-relevant">Not Relevant</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="observations">Observations:</label>
                        <textarea class="form-control" id="observations" rows="4" placeholder="Your observations..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-block btn-success">Submit</button>
                </div>

            </div>

        </form>
    </div>

@endsection
