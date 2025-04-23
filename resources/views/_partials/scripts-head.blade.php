<script>
    window.appEnv = '{{ config('app.env') }}';
    // window.appEnv = 'production';
</script>

<script>
    window.logJs = async function(logData) {
        logData = {
            'href': window.location.href,
            ...logData
        };
        fetch("{{ route('log-js') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(logData)
        });
    };
    if ('production' === window.appEnv) {
        try {
            window.addEventListener("error", (event) => {
                var logData = {
                    framework: 'js',
                    fileName: event.error.fileName,
                    columnNumber: event.error.columnNumber,
                    lineNumber: event.error.lineNumber,
                    message: event.message,
                    stack: event.error.stack,
                };
                window.logJs(logData);
            });
        } catch (e) {}
    }
</script>
