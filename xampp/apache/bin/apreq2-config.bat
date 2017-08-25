@rem = '--*-Perl-*--
@echo off
if "%OS%" == "Windows_NT" goto WinNT
perl -x -S "%0" %1 %2 %3 %4 %5 %6 %7 %8 %9
goto endofperl
:WinNT
"\xampp\perl\bin\perl.exe" -x -S %0 %*
if NOT "%COMSPEC%" == "%SystemRoot%\system32\cmd.exe" goto endofperl
if %errorlevel% == 9009 echo You do not have Perl in your PATH.
if errorlevel 1 goto script_failed_so_exit_with_non_zero_val 2>nul
goto endofperl
@rem ';
#!"\xampp\perl\bin\perl.exe"
#line 15
use strict;
use warnings;
use Getopt::Long;
use File::Spec::Functions qw(catfile catdir);

# ====================================================================
#
#  Copyright 2003-2006  The Apache Software Foundation
#
#  Licensed under the Apache License, Version 2.0 (the "License");
#  you may not use this file except in compliance with the License.
#  You may obtain a copy of the License at
#
#      http://www.apache.org/licenses/LICENSE-2.0
#
#  Unless required by applicable law or agreed to in writing, software
#  distributed under the License is distributed on an "AS IS" BASIS,
#  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
#  See the License for the specific language governing permissions and
#  limitations under the License.

# apreq2 script designed to allow easy command line access to apreq2
# configuration parameters.


sub usage {
    print << 'EOU';
Usage: apreq2-config [OPTION]

Known values for OPTION are:
  --prefix[=DIR]    change prefix to DIR
  --bindir          print location where binaries are installed
  --includedir      print location where headers are installed
  --libdir          print location where libraries are installed
  --cc              print C compiler name
  --cpp             print C preprocessor name and any required options
  --ld              print C linker name
  --cflags          print C compiler flags
  --cppflags        print cpp flags
  --includes        print include information
  --ldflags         print linker flags
  --libs            print additional libraries to link against
  --srcdir          print APR-util source directory
  --installbuilddir print APR-util build helper directory
  --link-ld         print link switch(es) for linking to APREQ
  --apreq-so-ext    print the extensions of shared objects on this platform
  --apreq-lib-file  print the name of the apreq lib file
  --version         print the APR-util version as a dotted triple
  --help            print this help

When linking, an application should do something like:
  APREQ_LIBS="\`apreq2-config --link-ld --libs\`"

An application should use the results of --cflags, --cppflags, --includes,
and --ldflags in their build process.

EOU
    exit(1);
}

my ${CC} = q[cl];
my ${LIBS} = q[];
my ${APREQ_DOTTED_VERSION} = q[2.7.1];
my ${APREQ_SOURCE_DIR} = q[\xampp\perl\bin\CPANPL~1\510~1.1\build\LIBAPR~1.12];
my ${installbuilddir} = q[\xampp\apache\build];
my ${bindir} = q[\xampp\apache\bin];
my ${LD} = q[link];
my ${CPP} = q[cl -nologo -E];
my ${APREQ_LIB_TARGET} = q[];
my ${APREQ_LIBNAME} = q[libapreq2.lib];
my ${includedir} = q[\xampp\apache\include];
my ${LDFLAGS} = q[ kernel32.lib /nologo /subsystem:windows /dll /machine:I386 ];
my ${exec_prefix} = q[\xampp\apache];
my ${datadir} = q[\xampp\apache];
my ${APREQ_SO_EXT} = q[dll];
my ${libdir} = q[\xampp\apache\lib];
my ${CFLAGS} = q[ /nologo /MD /W3 /O2 /D "WIN32" /D "_WINDOWS" /D "NDEBUG" ];
my ${SHELL} = q[C:\WINDOWS\system32\cmd.exe];
my ${CPPFLAGS} = q[];
my ${EXTRA_INCLUDES} = q[];
my ${APREQ_MAJOR_VERSION} = q[2];
my ${prefix} = q[\xampp\apache];

my %opts = ();
GetOptions(\%opts,
           'prefix:s',
           'bindir',
           'includedir',
           'libdir',
           'cc',
           'cpp',
           'ld',
           'cflags',
           'cppflags',
           'includes',
           'ldflags',
           'libs',
           'srcdir',
           'installbuilddir',
           'link-ld',
           'apreq-so-ext',
           'apreq-lib-file',
           'version',
           'help'
          ) or usage();

usage() if ($opts{help} or not %opts);

if (exists $opts{prefix} and $opts{prefix} eq "") {
    print qq{$prefix\n};
    exit(0);
}
my $user_prefix = defined $opts{prefix} ? $opts{prefix} : '';
my %user_dir;
if ($user_prefix) {
    foreach (qw(lib bin include build)) {
        $user_dir{$_} = catdir $user_prefix, $_;
    }
}
my $flags = '';

SWITCH : {
    local $\ = "\n";
    $opts{bindir} and do {
        print $user_prefix ? $user_dir{bin} : $bindir;
        last SWITCH;
    };
    $opts{includedir} and do {
        print $user_prefix ? $user_dir{include} : $includedir;
        last SWITCH;
    };
    $opts{libdir} and do {
        print $user_prefix ? $user_dir{lib} : $libdir;
        last SWITCH;
    };
    $opts{installbuilddir} and do {
        print $user_prefix ? $user_dir{build} : $installbuilddir;
        last SWITCH;
    };
    $opts{srcdir} and do {
        print $APREQ_SOURCE_DIR;
        last SWITCH;
    };
    $opts{cc} and do {
        print $CC;
        last SWITCH;
    };
    $opts{cpp} and do {
        print $CPP;
        last SWITCH;
    };
    $opts{ld} and do {
        print $LD;
        last SWITCH;
    };
    $opts{cflags} and $flags .= " $CFLAGS ";
    $opts{cppflags} and $flags .= " $CPPFLAGS ";
    $opts{includes} and do {
        my $inc = $user_prefix ? $user_dir{include} : $includedir;
        $flags .= qq{ /I"$inc" $EXTRA_INCLUDES };
    };
    $opts{ldflags} and $flags .= " $LDFLAGS ";
    $opts{libs} and $flags .= " $LIBS ";
    $opts{'link-ld'} and do {
        my $libpath = $user_prefix ? $user_dir{lib} : $libdir;
        $flags .= qq{ /libpath:"$libpath" $APREQ_LIBNAME };
    };
    $opts{'apreq-so-ext'} and do {
        print $APREQ_SO_EXT;
        last SWITCH;
    };
    $opts{'apreq-lib-file'} and do {
        my $full_apreqlib = $user_prefix ? 
            (catfile $user_dir{lib}, $APREQ_LIBNAME) :
                (catfile $libdir, $APREQ_LIBNAME);
        print $full_apreqlib;
        last SWITCH;
    };
    $opts{version} and do {
        print $APREQ_DOTTED_VERSION;
        last SWITCH;
    };
    print $flags if $flags;
}
exit(0);

__END__
:endofperl
