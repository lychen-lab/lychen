-- Registers the instance in the PoWA repository, unless it already is (same
-- alias, or same host and port), then asks the collector to reload its list of
-- servers. Run by init.sh as the repository's owner.
\getenv srv_host MCO_SERVER_HOST
\getenv srv_alias MCO_SERVER_ALIAS
\getenv powa_pw MCO_POWA_PASSWORD
SELECT powa_register_server(hostname => :'srv_host', alias => :'srv_alias',
         username => 'powa', password => :'powa_pw', dbname => 'powa', frequency => 60,
         extensions => '{pg_stat_kcache,pg_qualstats,pg_wait_sampling,pg_track_settings}')
 WHERE NOT EXISTS (SELECT 1 FROM powa_servers
                    WHERE alias = :'srv_alias' OR (hostname = :'srv_host' AND port = 5432));

-- The collector reads its servers at startup only: ask it to reload them (the
-- powa-web "Reload collector" button sends the same command). Lost if the
-- collector is not running yet, which then reads the new server when it starts.
NOTIFY powa_collector, 'RELOAD';
