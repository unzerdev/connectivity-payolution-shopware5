{extends file="parent:frontend/index/header.tpl"}

{block name='frontend_index_header_javascript_tracking'}
    {$smarty.block.parent}
    {if $payolutionIncludeFraudPrevention}
        <script type="text/javascript" src="https://h.online-metrix.net/fp/tags.js?org_id=363t8kgq&session_id={$sessionToken}"></script>
    {/if}
{/block}
