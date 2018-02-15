<?php

// Content request URI match
define ( 'swefcontent_shortcut_preg_match',     '<^/media/content/.*$>'             );

// Procedures
define ( 'swefcontent_call_permitsload',        'swefContentPermitsLoad'            );

// Other tokens
define ( 'swefcontent_col_read',                'read_permitted'                    );
define ( 'swefcontent_col_create',              'create_permitted'                  );
define ( 'swefcontent_col_update',              'update_permitted'                  );
define ( 'swefcontent_col_delete',              'delete_permitted'                  );
define ( 'swefcontent_error_contenttype',       'text/plain; charset=utf-8'         );

// Files
define ( 'swefcontent_file_dash',               __DIR__.'/SwefContent.dash.html'    );

?>
