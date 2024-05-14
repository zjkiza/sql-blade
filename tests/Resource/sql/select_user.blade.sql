SELECT u.id, u.email FROM users u
WHERE 1

@isset($emails)
    AND u.email IN ( :emails )
@endif

@isset($ids)
  AND u.id IN ( :ids )
@endif

ORDER BY
    @isset($ids)
        u.id
    @else
        u.email
    @endisset
;
