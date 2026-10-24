<?php

/*
| Bank soal DUMMY Structure and Written Expression — Paket B (40 butir).
|
| 15 soal kelengkapan kalimat + 25 soal identifikasi kesalahan, sesuai
| bentuk TOEFL ITP Level 1. Contoh latihan buatan sendiri, bukan butir ETS.
*/

return [
    /*
    | Kelengkapan kalimat (1–15)
    */
    [
        'kind' => 'completion',
        'q' => 'By the time we reached the station, the train _____.',
        'options' => ['A' => 'left', 'B' => 'has left', 'C' => 'had already left', 'D' => 'was leaving'],
        'key' => 'C',
    ],
    [
        'kind' => 'completion',
        'q' => 'The new library will be open to the public _____ March.',
        'options' => ['A' => 'in', 'B' => 'at', 'C' => 'on', 'D' => 'by'],
        'key' => 'A',
    ],
    [
        'kind' => 'completion',
        'q' => '_____ of the two candidates is suitable for the position.',
        'options' => ['A' => 'None', 'B' => 'Both', 'C' => 'Many', 'D' => 'Neither'],
        'key' => 'D',
    ],
    [
        'kind' => 'completion',
        'q' => 'She spoke as though she _____ the entire story.',
        'options' => ['A' => 'knows', 'B' => 'knew', 'C' => 'has known', 'D' => 'will know'],
        'key' => 'B',
    ],
    [
        'kind' => 'completion',
        'q' => 'The manager requested that the report _____ before noon.',
        'options' => ['A' => 'is submitted', 'B' => 'submits', 'C' => 'be submitted', 'D' => 'was submitted'],
        'key' => 'C',
    ],
    [
        'kind' => 'completion',
        'q' => 'I would rather you _____ nothing about the matter.',
        'options' => ['A' => 'said', 'B' => 'say', 'C' => 'have said', 'D' => 'will say'],
        'key' => 'A',
    ],
    [
        'kind' => 'completion',
        'q' => 'The exhibition was so popular _____ the museum extended its opening hours.',
        'options' => ['A' => 'so', 'B' => 'and', 'C' => 'but', 'D' => 'that'],
        'key' => 'D',
    ],
    [
        'kind' => 'completion',
        'q' => 'He hurried to the airport, only _____ that the flight had been cancelled.',
        'options' => ['A' => 'finding', 'B' => 'to find', 'C' => 'found', 'D' => 'finds'],
        'key' => 'B',
    ],
    [
        'kind' => 'completion',
        'q' => 'Not only _____ the answers, but she also checked them twice.',
        'options' => ['A' => 'she wrote', 'B' => 'did she wrote', 'C' => 'did she write', 'D' => 'she had written'],
        'key' => 'C',
    ],
    [
        'kind' => 'completion',
        'q' => 'The committee members could not agree on _____ to hold the event.',
        'options' => ['A' => 'where', 'B' => 'that', 'C' => 'what', 'D' => 'which'],
        'key' => 'A',
    ],
    [
        'kind' => 'completion',
        'q' => 'Ten years _____ since they last met.',
        'options' => ['A' => 'passed', 'B' => 'went', 'C' => 'came', 'D' => 'have passed'],
        'key' => 'D',
    ],
    [
        'kind' => 'completion',
        'q' => 'The volunteers worked tirelessly, _____ no complaint from anyone.',
        'options' => ['A' => 'although', 'B' => 'without', 'C' => 'despite', 'D' => 'unless'],
        'key' => 'B',
    ],
    [
        'kind' => 'completion',
        'q' => 'He is not the man he _____ five years ago.',
        'options' => ['A' => 'is', 'B' => 'was being', 'C' => 'used to be', 'D' => 'has been'],
        'key' => 'C',
    ],
    [
        'kind' => 'completion',
        'q' => 'So rapid was the growth of the company _____ it soon employed a thousand people.',
        'options' => ['A' => 'that', 'B' => 'what', 'C' => 'which', 'D' => 'than'],
        'key' => 'A',
    ],
    [
        'kind' => 'completion',
        'q' => 'The pictures on display were taken by students, many of _____ had never used a professional camera.',
        'options' => ['A' => 'them', 'B' => 'who', 'C' => 'that', 'D' => 'whom'],
        'key' => 'D',
    ],

    /*
    | Identifikasi kesalahan / written expression (16–40)
    */
    [
        'kind' => 'error',
        'q' => 'The teacher said (A) that the examination (B) were postponed (C) until next week (D).',
        'options' => ['A' => 'The teacher said', 'B' => 'that the examination', 'C' => 'were postponed', 'D' => 'until next week'],
        'key' => 'C',
    ],
    [
        'kind' => 'error',
        'q' => 'I am looking forward to (A) receive (B) your reply (C) soon (D).',
        'options' => ['A' => 'I am looking forward to', 'B' => 'receive', 'C' => 'your reply', 'D' => 'soon'],
        'key' => 'B',
    ],
    [
        'kind' => 'error',
        'q' => 'The informations (A) about the scholarship (B) was published (C) in the local newspaper (D).',
        'options' => ['A' => 'The informations', 'B' => 'about the scholarship', 'C' => 'was published', 'D' => 'in the local newspaper'],
        'key' => 'A',
    ],
    [
        'kind' => 'error',
        'q' => 'The meeting (A) will be hold (B) in the auditorium (C) tomorrow (D).',
        'options' => ['A' => 'The meeting', 'B' => 'will be hold', 'C' => 'in the auditorium', 'D' => 'tomorrow'],
        'key' => 'B',
    ],
    [
        'kind' => 'error',
        'q' => 'She is (A) the most (B) intelligent (C) student I have ever meet (D).',
        'options' => ['A' => 'She is', 'B' => 'the most', 'C' => 'intelligent', 'D' => 'student I have ever meet'],
        'key' => 'D',
    ],
    [
        'kind' => 'error',
        'q' => 'We discussed (A) about the problem (B) for two hours (C) yesterday (D).',
        'options' => ['A' => 'We discussed', 'B' => 'about the problem', 'C' => 'for two hours', 'D' => 'yesterday'],
        'key' => 'B',
    ],
    [
        'kind' => 'error',
        'q' => 'The children (A) was playing (B) in the garden (C) when it started to rain (D).',
        'options' => ['A' => 'The children', 'B' => 'was playing', 'C' => 'in the garden', 'D' => 'when it started to rain'],
        'key' => 'B',
    ],
    [
        'kind' => 'error',
        'q' => 'He didn’t (A) finished (B) his homework (C) before the class (D).',
        'options' => ['A' => 'He didn’t', 'B' => 'finished', 'C' => 'his homework', 'D' => 'before the class'],
        'key' => 'B',
    ],
    [
        'kind' => 'error',
        'q' => 'Neither of the students (A) were (B) able to answer (C) the last question (D).',
        'options' => ['A' => 'Neither of the students', 'B' => 'were', 'C' => 'able to answer', 'D' => 'the last question'],
        'key' => 'B',
    ],
    [
        'kind' => 'error',
        'q' => 'The cake (A) made by my sister (B) tastes (C) very deliciously (D).',
        'options' => ['A' => 'The cake', 'B' => 'made by my sister', 'C' => 'tastes', 'D' => 'very deliciously'],
        'key' => 'D',
    ],
    [
        'kind' => 'error',
        'q' => 'If I had known (A) about the meeting, (B) I would attend (C) it (D).',
        'options' => ['A' => 'If I had known', 'B' => 'about the meeting', 'C' => 'I would attend', 'D' => 'it'],
        'key' => 'C',
    ],
    [
        'kind' => 'error',
        'q' => 'The professor (A) explained the theory (B) so clearly (C) that everyone could understood (D) it.',
        'options' => ['A' => 'The professor', 'B' => 'explained the theory', 'C' => 'so clearly', 'D' => 'that everyone could understood'],
        'key' => 'D',
    ],
    [
        'kind' => 'error',
        'q' => 'She has been working (A) in the hospital (B) since five years (C) ago (D).',
        'options' => ['A' => 'She has been working', 'B' => 'in the hospital', 'C' => 'since five years', 'D' => 'ago'],
        'key' => 'C',
    ],
    [
        'kind' => 'error',
        'q' => 'The price of the tickets (A) have increased (B) since last year (C), according to the office (D).',
        'options' => ['A' => 'The price of the tickets', 'B' => 'have increased', 'C' => 'since last year', 'D' => 'according to the office'],
        'key' => 'B',
    ],
    [
        'kind' => 'error',
        'q' => 'The teacher, together with (A) some students, (B) were (C) planting trees last Friday (D).',
        'options' => ['A' => 'The teacher, together with', 'B' => 'some students', 'C' => 'were', 'D' => 'planting trees last Friday'],
        'key' => 'C',
    ],
    [
        'kind' => 'error',
        'q' => 'It is time (A) that we (B) leave (C) the classroom (D).',
        'options' => ['A' => 'It is time', 'B' => 'that we', 'C' => 'leave', 'D' => 'the classroom'],
        'key' => 'C',
    ],
    [
        'kind' => 'error',
        'q' => 'The number of applicants (A) has risen (B) dramatic (C) this year (D).',
        'options' => ['A' => 'The number of applicants', 'B' => 'has risen', 'C' => 'dramatic', 'D' => 'this year'],
        'key' => 'C',
    ],
    [
        'kind' => 'error',
        'q' => 'He spoke (A) English (B) very good (C) for a beginner (D).',
        'options' => ['A' => 'He spoke', 'B' => 'English', 'C' => 'very good', 'D' => 'for a beginner'],
        'key' => 'C',
    ],
    [
        'kind' => 'error',
        'q' => 'Each of the answers (A) were checked (B) twice (C) by the examiners (D).',
        'options' => ['A' => 'Each of the answers', 'B' => 'were checked', 'C' => 'twice', 'D' => 'by the examiners'],
        'key' => 'B',
    ],
    [
        'kind' => 'error',
        'q' => 'Although he was tired, (A) but he continued (B) working (C) until midnight (D).',
        'options' => ['A' => 'Although he was tired', 'B' => 'but he continued', 'C' => 'working', 'D' => 'until midnight'],
        'key' => 'B',
    ],
    [
        'kind' => 'error',
        'q' => 'The news (A) were announced (B) by the principal (C) this morning (D).',
        'options' => ['A' => 'The news', 'B' => 'were announced', 'C' => 'by the principal', 'D' => 'this morning'],
        'key' => 'B',
    ],
    [
        'kind' => 'error',
        'q' => 'He asked me (A) where (B) did I buy (C) the bicycle (D).',
        'options' => ['A' => 'He asked me', 'B' => 'where', 'C' => 'did I buy', 'D' => 'the bicycle'],
        'key' => 'C',
    ],
    [
        'kind' => 'error',
        'q' => 'The workers (A) has been building (B) the bridge (C) for two years (D).',
        'options' => ['A' => 'The workers', 'B' => 'has been building', 'C' => 'the bridge', 'D' => 'for two years'],
        'key' => 'B',
    ],
    [
        'kind' => 'error',
        'q' => 'I (A) suggested him (B) to apply (C) for the job (D).',
        'options' => ['A' => 'I', 'B' => 'suggested him', 'C' => 'to apply', 'D' => 'for the job'],
        'key' => 'B',
    ],
    [
        'kind' => 'error',
        'q' => 'He is (A) capable (B) to solve (C) the problem (D).',
        'options' => ['A' => 'He is', 'B' => 'capable', 'C' => 'to solve', 'D' => 'the problem'],
        'key' => 'C',
    ],
];
