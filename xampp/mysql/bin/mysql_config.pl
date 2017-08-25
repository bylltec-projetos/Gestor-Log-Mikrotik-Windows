#!\xampp\perl\bin\perl.exe
use strict;
use warnings;
use Getopt::Long;

# Copyright (C) 2005 MySQL AB & MySQL Finland AB & TCX DataKonsult AB
# 
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

# This script reports various configuration settings that may be needed
# when using the MySQL client library.


my $basedir = q{\xampp\mysql};
my $ldata = q{\xampp\mysql\data};
my $execdir = q{\xampp\mysql\bin};
my $bindir = q{\xampp\mysql\bin};
my $pkglibdir = q{\xampp\mysql\lib\opt};
my $pkgincludedir = q{\xampp\mysql\include};
my $ldflags = q{};
my $client_libs = q{-llibmysql -lzlib};
my $version = q{5.1.41};
my $port = q{};
my $libs = q{ -L"\xampp\mysql\lib\opt" -llibmysql -lzlib};
my $cflags = q{-I"\xampp\mysql\include"};
my $embedded_libs = q{ -L"\xampp\mysql\lib\opt"};

my %opts = ();
GetOptions(\%opts,
           'cflags',
           'libs',
           'port',
           'version',
           'libmysqld-libs',
           'embedded',
           'embedded-libs',
           'help',
          ) or usage();

usage() if ($opts{help} or not %opts);

SWITCH : {
  local $\ = "\n";
  $opts{cflags} and do {
    print $cflags;
    last SWITCH;
  };
  $opts{libs} and do {
    print $libs;
    last SWITCH;
  };
  $opts{port} and do {
    print $port;
    last SWITCH;
  };
  $opts{version} and do {
    print $version;
    last SWITCH;
  };
  ($opts{'libmysqld-libs'} or $opts{embedded} or $opts{'libmysqld-libs'} )
    and do {
      print $embedded_libs;
      last SWITCH;
    };
  usage();
}

exit(0);

sub usage {
  print << "EOU";
Usage: $0 [OPTIONS]

Options:
        --cflags         [$cflags]
        --libs           [$libs]
        --port           [$port]
        --version        [$version]
	--libmysqld-libs [$embedded_libs]
EOU
    exit(1);
}




