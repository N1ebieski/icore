@render('icore::category.post.categoryComponent')
@render('icore::archive.post.archiveComponent')
@render('icore::tag.post.tagComponent', [
    'limit' => 25, 
    'cats' => $catsAsArray['self'] ?? null
])
