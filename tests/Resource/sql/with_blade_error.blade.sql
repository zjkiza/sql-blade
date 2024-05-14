SELECT u.id, u.email FROM users u
WHERE 1
@issettt($ids)
  AND u.id IN ( :ids )
@endif
;
