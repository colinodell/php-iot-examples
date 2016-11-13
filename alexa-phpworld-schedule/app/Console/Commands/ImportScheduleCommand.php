<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class ImportScheduleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports the php[world] schedule from the conference website.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Manually scraped from the site by viewing the AJAX requests for each day
        $timestamps = [1479081600, 1479168000, 1479254400, 1479340800, 1479427200];

        foreach ($timestamps as $timestamp) {
            $this->importSchedule($timestamp);
        }
    }

    private function importSchedule($timestamp)
    {
        $client = new Client([
            'base_uri' => '',
            'timeout' => 10,
        ]);

        $response = $client->post('https://world.phparch.com/wp-admin/admin-ajax.php', [
            'form_params' => [
                'action' => 'get_schedule',
                'data-timestamp' => $timestamp,
                'data-location' => 0,
                'data-track' => 0,
                'data-page' => 1,
                'data-max-items' => 100,
            ],
            'headers' => [
                'Accept' => 'application/javascript',
                // Pretend the request from the website - sorry Eli!
                'X-Requested-With' => 'XMLHttpRequest',
                'Origin' => 'https://world.phparch.com',
                'Referer' => 'https://world.phparch.com/schedule/',
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        foreach ($data['sessions'] as $session) {
            $speakers = implode('|', array_map(function($speaker){
                return $speaker['post_title'];
            }, $session['speakers']));

            $tracks = implode('|', array_map(function($track){
                return $track['name'];
            }, $session['tracks']));

            $start = Carbon::parse($session['date'].' '.$session['time'], 'America/New_York');
            $end = Carbon::parse($session['date'].' '.$session['end_time'], 'America/New_York');

            DB::insert('insert into sessions (start, `end`, room, title, abstract, speakers, tracks) values (?, ?, ?, ?, ?, ?, ?)', [
                $start->getTimestamp(),
                $end->getTimestamp(),
                $session['location'],
                $session['post_title'],
                $session['post_excerpt'],
                $speakers,
                $tracks,
            ]);
        }
    }
}
