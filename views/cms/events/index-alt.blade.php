<?php
use Illuminate\Support\Facades\Auth;
use Soda\Analytics\Components\GoogleAPI;

$config = GoogleConfig::get();
$logged_in = Auth::guard('soda-analytics')->check() && Auth::guard('soda-analytics')->validGoogle();
?>

@extends(soda_cms_view_path('layouts.inner'))

@section('breadcrumb')
    <ol class="breadcrumb" xmlns:v-on="http://www.w3.org/1999/xhtml">
        <li><a href="{{ route('soda.home') }}">Home</a></li>
        <li>Analytics</li>
        <li class="active">Events</li>
    </ol>
@stop

@section('head.title')
    <title>Analytics | Events</title>
@endsection

@include(soda_cms_view_path('partials.heading'), [
    'icon'        => 'fa fa-share-alt',
    'title'       => 'Analytics | Events',
])

@section('content')
    @foreach($reports as $report)
        <?php
            $header = $report->getColumnHeader();
            $dimensionHeaders = $header->getDimensions();
            $metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
            $rows = $report->getData()->getRows();
        ?>
        <table class="table table-striped sortable middle">
            <thead>
                <tr>
                    @foreach($dimensionHeaders as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                    @foreach($metricHeaders as $header)
                        <th>{{ $header->getName() }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="sortable" data-entityname="content">
                @foreach($rows as $row)
                    <?php
                        $dimensions = $row->getDimensions();
                        $metrics = $row->getMetrics();
                    ?>
                    <tr>
                        @foreach($dimensions as $dimension)
                            <td>
                                {{ $dimension }}
                            </td>
                        @endforeach
                        @foreach($metrics as $metric)
                            <?php $values = $metric->getValues(); ?>
                            @foreach($values as $value)
                                <td>
                                    {{ $value }}
                                </td>
                            @endforeach
                        @endforeach
                    </tr>
                @endforeach
                @if( count($rows) <= 0 )
                    <tr>
                        <td colspan="5">No content to display</td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endforeach
@endsection

@section('footer.js')
    @parent
    <script src="/soda/analytics/js/app.js"></script>
@endsection

