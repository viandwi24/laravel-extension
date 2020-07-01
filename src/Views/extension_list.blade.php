<style>
    table.extension-table tr .action * {
        transition: all .2s ease-in-out;
        color: rgb(209, 209, 209);
    }
    table.extension-table tr:hover .action * {
        color: black;
    }
</style>
<table class="table table-bordered table-hover extension-table mb-0">
    <thead>
        <tr>
            <th>Extension</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($extensions as $extension)
            <tr>
                <td>
                    <b>{{ $extension->config->name }}</b>
                    <div class="action">                        
                        @if ($extension->active)
                        <a href="{{ route('extension.disable', [$extension->name]) }}">
                            Disable
                        </a>
                        @else
                        <a href="{{ route('extension.enable', [$extension->name]) }}">
                            Enable
                        </a>                        
                        @endif
                        /
                        <a href="{{ route('extension.inspect', [$extension->name]) }}">
                            Inspect
                        </a>
                    </div>
                </td>
                <td>
                    <p class="mb-0">
                        {{ $extension->config->description }}
                    </p>
                    <p class="mb-0">
                        <small>
                            <span class="badge {{ ($extension->active) ? "bg-success" : "bg-danger" }}">
                                {{ ($extension->active) ? "Enable" : "Disable" }}
                            </span> |
                            <span>Version {{ $extension->config->version }}</span> |
                            <span>By {{ $extension->config->author->name }}</span> |
                            <a target="_blank" href="{{ $extension->config->author->site }}">See Author Website</a>
                        </small>
                    </p>
                    @if (count($extension->errors) > 0)
                        <p style="color: red;" class="mt-0">
                            Extension Have {{ count($extension->errors) }} error,
                            Click "inspect" for see detail.
                        </p>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>