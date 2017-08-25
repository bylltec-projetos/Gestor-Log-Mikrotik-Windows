#ifndef MODPERL_DIRECTIVES_H
#define MODPERL_DIRECTIVES_H

/*
 * *********** WARNING **************
 * This file generated by ModPerl::Code/0.01
 * Any changes made here will be lost
 * ***********************************
 * 01: lib/ModPerl/Code.pm:733
 * 02: lib/ModPerl/Code.pm:759
 * 03: C:\xampp\perl\bin\.cpanplus\5.10.1\build\mod_perl-2.0.4\Makefile.PL:383
 * 04: C:\xampp\perl\bin\.cpanplus\5.10.1\build\mod_perl-2.0.4\Makefile.PL:96
 * 05: \xampp\perl\bin\cpanp-run-perl.bat:21
 */

const char *modperl_cmd_process_connection_handlers(cmd_parms *parms, void *dummy, const char *arg);

#define MP_CMD_PROCESS_CONNECTION_ENTRY \
AP_INIT_ITERATE("PerlProcessConnectionHandler", modperl_cmd_process_connection_handlers, NULL, \
 RSRC_CONF, "Subroutine name")

const char *modperl_cmd_child_init_handlers(cmd_parms *parms, void *dummy, const char *arg);

#define MP_CMD_CHILD_INIT_ENTRY \
AP_INIT_ITERATE("PerlChildInitHandler", modperl_cmd_child_init_handlers, NULL, \
 RSRC_CONF, "Subroutine name")

const char *modperl_cmd_child_exit_handlers(cmd_parms *parms, void *dummy, const char *arg);

#define MP_CMD_CHILD_EXIT_ENTRY \
AP_INIT_ITERATE("PerlChildExitHandler", modperl_cmd_child_exit_handlers, NULL, \
 RSRC_CONF, "Subroutine name")

const char *modperl_cmd_pre_connection_handlers(cmd_parms *parms, void *dummy, const char *arg);

#define MP_CMD_PRE_CONNECTION_ENTRY \
AP_INIT_ITERATE("PerlPreConnectionHandler", modperl_cmd_pre_connection_handlers, NULL, \
 RSRC_CONF, "Subroutine name")

const char *modperl_cmd_header_parser_handlers(cmd_parms *parms, void *dummy, const char *arg);

#define MP_CMD_HEADER_PARSER_ENTRY \
AP_INIT_ITERATE("PerlHeaderParserHandler", modperl_cmd_header_parser_handlers, NULL, \
 OR_ALL, "Subroutine name")

const char *modperl_cmd_access_handlers(cmd_parms *parms, void *dummy, const char *arg);

#define MP_CMD_ACCESS_ENTRY \
AP_INIT_ITERATE("PerlAccessHandler", modperl_cmd_access_handlers, NULL, \
 OR_ALL, "Subroutine name")

const char *modperl_cmd_authen_handlers(cmd_parms *parms, void *dummy, const char *arg);

#define MP_CMD_AUTHEN_ENTRY \
AP_INIT_ITERATE("PerlAuthenHandler", modperl_cmd_authen_handlers, NULL, \
 OR_ALL, "Subroutine name")

const char *modperl_cmd_authz_handlers(cmd_parms *parms, void *dummy, const char *arg);

#define MP_CMD_AUTHZ_ENTRY \
AP_INIT_ITERATE("PerlAuthzHandler", modperl_cmd_authz_handlers, NULL, \
 OR_ALL, "Subroutine name")

const char *modperl_cmd_type_handlers(cmd_parms *parms, void *dummy, const char *arg);

#define MP_CMD_TYPE_ENTRY \
AP_INIT_ITERATE("PerlTypeHandler", modperl_cmd_type_handlers, NULL, \
 OR_ALL, "Subroutine name")

const char *modperl_cmd_fixup_handlers(cmd_parms *parms, void *dummy, const char *arg);

#define MP_CMD_FIXUP_ENTRY \
AP_INIT_ITERATE("PerlFixupHandler", modperl_cmd_fixup_handlers, NULL, \
 OR_ALL, "Subroutine name")

const char *modperl_cmd_response_handlers(cmd_parms *parms, void *dummy, const char *arg);

#define MP_CMD_RESPONSE_ENTRY \
AP_INIT_ITERATE("PerlResponseHandler", modperl_cmd_response_handlers, NULL, \
 OR_ALL, "Subroutine name")

const char *modperl_cmd_log_handlers(cmd_parms *parms, void *dummy, const char *arg);

#define MP_CMD_LOG_ENTRY \
AP_INIT_ITERATE("PerlLogHandler", modperl_cmd_log_handlers, NULL, \
 OR_ALL, "Subroutine name")

const char *modperl_cmd_cleanup_handlers(cmd_parms *parms, void *dummy, const char *arg);

#define MP_CMD_CLEANUP_ENTRY \
AP_INIT_ITERATE("PerlCleanupHandler", modperl_cmd_cleanup_handlers, NULL, \
 OR_ALL, "Subroutine name")

const char *modperl_cmd_input_filter_handlers(cmd_parms *parms, void *dummy, const char *arg);

#define MP_CMD_INPUT_FILTER_ENTRY \
AP_INIT_ITERATE("PerlInputFilterHandler", modperl_cmd_input_filter_handlers, NULL, \
 OR_ALL, "Subroutine name")

const char *modperl_cmd_output_filter_handlers(cmd_parms *parms, void *dummy, const char *arg);

#define MP_CMD_OUTPUT_FILTER_ENTRY \
AP_INIT_ITERATE("PerlOutputFilterHandler", modperl_cmd_output_filter_handlers, NULL, \
 OR_ALL, "Subroutine name")

const char *modperl_cmd_post_read_request_handlers(cmd_parms *parms, void *dummy, const char *arg);

#define MP_CMD_POST_READ_REQUEST_ENTRY \
AP_INIT_ITERATE("PerlPostReadRequestHandler", modperl_cmd_post_read_request_handlers, NULL, \
 RSRC_CONF, "Subroutine name")

const char *modperl_cmd_trans_handlers(cmd_parms *parms, void *dummy, const char *arg);

#define MP_CMD_TRANS_ENTRY \
AP_INIT_ITERATE("PerlTransHandler", modperl_cmd_trans_handlers, NULL, \
 RSRC_CONF, "Subroutine name")

const char *modperl_cmd_map_to_storage_handlers(cmd_parms *parms, void *dummy, const char *arg);

#define MP_CMD_MAP_TO_STORAGE_ENTRY \
AP_INIT_ITERATE("PerlMapToStorageHandler", modperl_cmd_map_to_storage_handlers, NULL, \
 RSRC_CONF, "Subroutine name")

const char *modperl_cmd_open_logs_handlers(cmd_parms *parms, void *dummy, const char *arg);

#define MP_CMD_OPEN_LOGS_ENTRY \
AP_INIT_ITERATE("PerlOpenLogsHandler", modperl_cmd_open_logs_handlers, NULL, \
 RSRC_CONF, "Subroutine name")

const char *modperl_cmd_post_config_handlers(cmd_parms *parms, void *dummy, const char *arg);

#define MP_CMD_POST_CONFIG_ENTRY \
AP_INIT_ITERATE("PerlPostConfigHandler", modperl_cmd_post_config_handlers, NULL, \
 RSRC_CONF, "Subroutine name")

#define MP_CMD_ENTRIES \
MP_CMD_PROCESS_CONNECTION_ENTRY, \
MP_CMD_CHILD_INIT_ENTRY, \
MP_CMD_CHILD_EXIT_ENTRY, \
MP_CMD_PRE_CONNECTION_ENTRY, \
MP_CMD_HEADER_PARSER_ENTRY, \
MP_CMD_ACCESS_ENTRY, \
MP_CMD_AUTHEN_ENTRY, \
MP_CMD_AUTHZ_ENTRY, \
MP_CMD_TYPE_ENTRY, \
MP_CMD_FIXUP_ENTRY, \
MP_CMD_RESPONSE_ENTRY, \
MP_CMD_LOG_ENTRY, \
MP_CMD_CLEANUP_ENTRY, \
MP_CMD_INPUT_FILTER_ENTRY, \
MP_CMD_OUTPUT_FILTER_ENTRY, \
MP_CMD_POST_READ_REQUEST_ENTRY, \
MP_CMD_TRANS_ENTRY, \
MP_CMD_MAP_TO_STORAGE_ENTRY, \
MP_CMD_OPEN_LOGS_ENTRY, \
MP_CMD_POST_CONFIG_ENTRY
#endif /* MODPERL_DIRECTIVES_H */