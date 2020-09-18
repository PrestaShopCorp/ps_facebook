{if !empty($content)}
    {literal}
        <script>
            fbq('{/literal}{$track|escape:'htmlall':'UTF-8'}{literal}', '{/literal}{$type|escape:'htmlall':'UTF-8'}{literal}', {/literal}{$content nofilter}{literal});
        </script>
    {/literal}
{else if !empty($type) && empty($content)}
    {literal}
        <script>
            fbq('{/literal}{$track|escape:'htmlall':'UTF-8'}{literal}', '{/literal}{$type|escape:'htmlall':'UTF-8'}{literal}');
        </script>
    {/literal}
{/if}
