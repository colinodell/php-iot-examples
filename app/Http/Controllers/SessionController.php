<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Develpr\AlexaApp\Facades\Alexa;
use Develpr\AlexaApp\Request\AlexaRequest;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class SessionController extends Controller
{
    public function getSessions(AlexaRequest $request)
    {
        $request->getIntent();
        $time = $request->slot('time') ?: Carbon::now()->format('G:i');
        $day = $request->slot('day') ?: Carbon::now()->format('Y-m-d');
        $room = $request->slot('room');

        try {
            $sessions = $this->findSessions($time, $day, $room);

            if (count($sessions) === 0) {
                // No sessions found
                return Alexa::say('Sorry, I couldn\'t find any sessions at that time.');
            } elseif (count($sessions) === 1) {
                return Alexa::say($this->getSessionText(reset($sessions)));
            } else {
                $text = 'I found ' . count($sessions) . ' sessions. ';
                foreach ($sessions as $session) {
                    $text .= $this->getSessionText($session);
                }

                return Alexa::say($text);
            }
        } catch (\Exception $ex) {
            return Alexa::say('Sorry, I\'m having trouble searching for sessions right now.');
        }
    }

    /**
     * @param string|null $time
     * @param string|null $day
     * @param string|null $room
     *
     * @return array
     *
     * @todo: Refactor into a service
     */
    private function findSessions($time, $day, $room)
    {
        if (empty($day)) {
            $day = Carbon::now()->format('Y-m-d');
        }

        if (empty($time)) {
            $time = date('H:i');
        } elseif (strlen($time) === 2) {
            // We can get a time indicator for certain utterances like "morning",
            // so we'll need to convert those to a certain time for our purposes
            //
            switch ($time) {
                case 'MO':
                    $time = '08:00';
                    break;
                case 'AF':
                    $time = '12:30';
                    break;
                case 'EV':
                    $time = '';
                    break;
                case 'NI':
                    $time = '';
                    break;
            }
        }

        $timestamp = strtotime($day.' '.$time);

        /** @var Builder $query */
        $query = DB::table('sessions');
        $query->where('start', '<=', $timestamp);
        $query->where('end', '>', $timestamp);

        if (!empty($room)) {
            $query->where('room', '=', $room);
        }

        $query->orderBy('start');

        return $query->get();
    }

    /**
     * @param \stdClass $session
     *
     * @return string
     */
    private function getSessionText($session)
    {
        $speakers = implode(' and ', explode('|', $session->speakers));

        // TODO: Ensure Alexa can pronounce the time correctly.
        $time = date('g:i a', $session->start);

        return sprintf('%s will be giving a talk entitled %s in %s at %s.', $speakers, $session->title, $session->room, $time);
    }
}
