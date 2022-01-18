{extends file="parent:frontend/index/index.tpl"}

{block name='frontend_index_no_script_message'}
    {$smarty.block.parent}
    {if $payolutionIncludeFraudPrevention}
        <noscript>
            <iframe style="width: 100px; height: 100px; border: 0; position: absolute; top: -5000px;"
                    src="https://h.online-metrix.net/fp/tags?org_id=363t8kgq&session_id={payolutionsessiontoken}">
            </iframe>
        </noscript>
    {/if}
{/block}
