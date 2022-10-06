<x-icore::category.post.category-component />
<x-icore::archive.post.archive-component />
<x-icore::tag.post.tag-component
    limit="25"
    :cats="$catsAsArray['self'] ?? null"
/>
