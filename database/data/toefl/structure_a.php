<?php

/*
| Bank soal DUMMY Structure and Written Expression — Paket A (40 butir).
|
| Terdiri dari 15 soal kelengkapan kalimat (sentence completion) dan
| 25 soal identifikasi kesalahan (written expression), sesuai bentuk
| TOEFL ITP Level 1. Seluruh butir adalah contoh latihan buatan sendiri,
| bukan butir resmi ETS.
*/

return [
    /*
    | Kelengkapan kalimat (1–15)
    */
    [
        'kind' => 'completion',
        'q' => 'The committee _____ the proposal until next Friday.',
        'options' => ['A' => 'will not discuss', 'B' => 'does not discuss', 'C' => 'had not discussed', 'D' => 'not discussed'],
        'key' => 'A',
    ],
    [
        'kind' => 'completion',
        'q' => 'Neither the teacher nor the students _____ aware of the schedule change.',
        'options' => ['A' => 'was', 'B' => 'were', 'C' => 'is', 'D' => 'has been'],
        'key' => 'B',
    ],
    [
        'kind' => 'completion',
        'q' => 'If she had studied harder, she _____ the examination.',
        'options' => ['A' => 'will pass', 'B' => 'would pass', 'C' => 'would have passed', 'D' => 'has passed'],
        'key' => 'C',
    ],
    [
        'kind' => 'completion',
        'q' => 'Hardly _____ the classroom when the bell rang.',
        'options' => ['A' => 'he had entered', 'B' => 'he has entered', 'C' => 'did he enter', 'D' => 'had he entered'],
        'key' => 'D',
    ],
    [
        'kind' => 'completion',
        'q' => 'The new regulation aims at _____ the number of accidents on the road.',
        'options' => ['A' => 'reduce', 'B' => 'reduced', 'C' => 'reduction', 'D' => 'reducing'],
        'key' => 'D',
    ],
    [
        'kind' => 'completion',
        'q' => 'It is essential that every student _____ the application form before Monday.',
        'options' => ['A' => 'would fill out', 'B' => 'filled out', 'C' => 'fill out', 'D' => 'filling out'],
        'key' => 'C',
    ],
    [
        'kind' => 'completion',
        'q' => 'The more you practise, the _____ you will become.',
        'options' => ['A' => 'better', 'B' => 'good', 'C' => 'best', 'D' => 'well'],
        'key' => 'A',
    ],
    [
        'kind' => 'completion',
        'q' => 'She was prevented from attending the meeting _____ her illness.',
        'options' => ['A' => 'because', 'B' => 'due to', 'C' => 'although', 'D' => 'in spite'],
        'key' => 'B',
    ],
    [
        'kind' => 'completion',
        'q' => 'Not until the lights went out _____ that the building was on fire.',
        'options' => ['A' => 'they realized', 'B' => 'they had realized', 'C' => 'did they realize', 'D' => 'had they realized'],
        'key' => 'C',
    ],
    [
        'kind' => 'completion',
        'q' => 'The documents must be signed by the director, _____ authority is required for contracts over fifty million rupiah.',
        'options' => ['A' => 'whose', 'B' => 'which', 'C' => 'that', 'D' => 'what'],
        'key' => 'A',
    ],
    [
        'kind' => 'completion',
        'q' => 'By next year, the new bridge _____ completed.',
        'options' => ['A' => 'will be', 'B' => 'will have been', 'C' => 'is', 'D' => 'has been'],
        'key' => 'B',
    ],
    [
        'kind' => 'completion',
        'q' => 'He spoke so _____ that hardly anyone could understand him.',
        'options' => ['A' => 'quiet', 'B' => 'more quiet', 'C' => 'most quiet', 'D' => 'quietly'],
        'key' => 'D',
    ],
    [
        'kind' => 'completion',
        'q' => 'The factory has reduced its waste _____ thirty percent since 2020.',
        'options' => ['A' => 'at', 'B' => 'by', 'C' => 'on', 'D' => 'in'],
        'key' => 'B',
    ],
    [
        'kind' => 'completion',
        'q' => 'Were I you, I _____ the offer immediately.',
        'options' => ['A' => 'accept', 'B' => 'accepted', 'C' => 'would accept', 'D' => 'will accept'],
        'key' => 'C',
    ],
    [
        'kind' => 'completion',
        'q' => 'The reason he gave for being late _____ acceptable to the manager.',
        'options' => ['A' => 'were not', 'B' => 'was not', 'C' => 'are not', 'D' => 'does not'],
        'key' => 'B',
    ],

    /*
    | Identifikasi kesalahan / written expression (16–40)
    */
    [
        'kind' => 'error',
        'q' => 'The students (A) was told (B) to hand in (C) their reports (D) tomorrow.',
        'options' => ['A' => 'was told', 'B' => 'to hand in', 'C' => 'their reports', 'D' => 'tomorrow'],
        'key' => 'A',
    ],
    [
        'kind' => 'error',
        'q' => 'Each of the candidates (A) have submitted (B) the required documents (C) before the deadline (D).',
        'options' => ['A' => 'Each of the candidates', 'B' => 'have submitted', 'C' => 'the required documents', 'D' => 'before the deadline'],
        'key' => 'B',
    ],
    [
        'kind' => 'error',
        'q' => 'The quality (A) of the products (B) were checked (C) before shipping (D).',
        'options' => ['A' => 'The quality', 'B' => 'of the products', 'C' => 'were checked', 'D' => 'before shipping'],
        'key' => 'C',
    ],
    [
        'kind' => 'error',
        'q' => 'She (A) has been working (B) here (C) since three years (D) ago.',
        'options' => ['A' => 'has been working', 'B' => 'here', 'C' => 'since three years', 'D' => 'ago'],
        'key' => 'C',
    ],
    [
        'kind' => 'error',
        'q' => 'The new policy (A) will helps (B) reduce (C) air pollution (D) in the city.',
        'options' => ['A' => 'The new policy', 'B' => 'will helps', 'C' => 'reduce', 'D' => 'air pollution'],
        'key' => 'B',
    ],
    [
        'kind' => 'error',
        'q' => 'Neither the boys (A) nor the girls (B) was (C) aware of the new rule (D).',
        'options' => ['A' => 'Neither the boys', 'B' => 'nor the girls', 'C' => 'was', 'D' => 'aware of the new rule'],
        'key' => 'C',
    ],
    [
        'kind' => 'error',
        'q' => 'He is one of those students (A) who (B) has always (C) been punctual (D).',
        'options' => ['A' => 'one of those students', 'B' => 'who', 'C' => 'has always', 'D' => 'been punctual'],
        'key' => 'C',
    ],
    [
        'kind' => 'error',
        'q' => 'The teacher, along with her students, (A) are going (B) to the museum (C) this Friday (D).',
        'options' => ['A' => 'are going', 'B' => 'to the museum', 'C' => 'this Friday', 'D' => 'The teacher'],
        'key' => 'A',
    ],
    [
        'kind' => 'error',
        'q' => 'I look forward to (A) hear (B) from you (C) soon (D).',
        'options' => ['A' => 'I look forward to', 'B' => 'hear', 'C' => 'from you', 'D' => 'soon'],
        'key' => 'B',
    ],
    [
        'kind' => 'error',
        'q' => 'Despite of (A) the heavy rain, (B) the match (C) continued as scheduled (D).',
        'options' => ['A' => 'Despite of', 'B' => 'the heavy rain', 'C' => 'the match', 'D' => 'continued as scheduled'],
        'key' => 'A',
    ],
    [
        'kind' => 'error',
        'q' => 'The number of tourists (A) visiting Bali (B) have increased (C) every year (D).',
        'options' => ['A' => 'The number of tourists', 'B' => 'visiting Bali', 'C' => 'have increased', 'D' => 'every year'],
        'key' => 'C',
    ],
    [
        'kind' => 'error',
        'q' => 'He asked me if (A) I could lend (B) him some money, and I said (C) I don’t have (D) any.',
        'options' => ['A' => 'He asked me if', 'B' => 'I could lend', 'C' => 'and I said', 'D' => 'I don’t have'],
        'key' => 'D',
    ],
    [
        'kind' => 'error',
        'q' => 'My brother (A) is taller (B) than (C) me (D).',
        'options' => ['A' => 'My brother', 'B' => 'is taller', 'C' => 'than', 'D' => 'me'],
        'key' => 'D',
    ],
    [
        'kind' => 'error',
        'q' => 'The committee (A) have decided (B) to postpone (C) the event (D).',
        'options' => ['A' => 'have decided', 'B' => 'to postpone', 'C' => 'the event', 'D' => 'The committee'],
        'key' => 'A',
    ],
    [
        'kind' => 'error',
        'q' => 'She (A) suggested (B) me to take (C) a short vacation (D).',
        'options' => ['A' => 'She', 'B' => 'suggested', 'C' => 'me to take', 'D' => 'a short vacation'],
        'key' => 'C',
    ],
    [
        'kind' => 'error',
        'q' => 'Every student (A) must (B) submit their assignment (C) before Friday (D).',
        'options' => ['A' => 'Every student', 'B' => 'must', 'C' => 'submit their assignment', 'D' => 'before Friday'],
        'key' => 'C',
    ],
    [
        'kind' => 'error',
        'q' => 'The news (A) about the accident (B) were shocking (C) to everyone (D).',
        'options' => ['A' => 'The news', 'B' => 'about the accident', 'C' => 'were shocking', 'D' => 'to everyone'],
        'key' => 'C',
    ],
    [
        'kind' => 'error',
        'q' => 'Walking through the garden, (A) the flowers (B) caught (C) Maria’s attention (D).',
        'options' => ['A' => 'Walking through the garden', 'B' => 'the flowers', 'C' => 'caught', 'D' => 'Maria’s attention'],
        'key' => 'A',
    ],
    [
        'kind' => 'error',
        'q' => 'Both of the girls (A) has finished (B) their homework (C) early (D).',
        'options' => ['A' => 'Both of the girls', 'B' => 'has finished', 'C' => 'their homework', 'D' => 'early'],
        'key' => 'B',
    ],
    [
        'kind' => 'error',
        'q' => 'He spoke (A) so quickly (B) that (C) nobody understand (D) him.',
        'options' => ['A' => 'He spoke', 'B' => 'so quickly', 'C' => 'that', 'D' => 'nobody understand'],
        'key' => 'D',
    ],
    [
        'kind' => 'error',
        'q' => 'The furniture in the hall (A) are made (B) of teak (C) from Java (D).',
        'options' => ['A' => 'The furniture in the hall', 'B' => 'are made', 'C' => 'of teak', 'D' => 'from Java'],
        'key' => 'B',
    ],
    [
        'kind' => 'error',
        'q' => 'I have (A) lived (B) in Pekanbaru (C) since ten years (D).',
        'options' => ['A' => 'I have', 'B' => 'lived', 'C' => 'in Pekanbaru', 'D' => 'since ten years'],
        'key' => 'D',
    ],
    [
        'kind' => 'error',
        'q' => 'The manager along with his staff (A) were present (B) at the opening (C) of the new branch (D).',
        'options' => ['A' => 'were present', 'B' => 'at the opening', 'C' => 'of the new branch', 'D' => 'The manager'],
        'key' => 'A',
    ],
    [
        'kind' => 'error',
        'q' => 'She was (A) too tired (B) that (C) she fell asleep immediately (D).',
        'options' => ['A' => 'She was', 'B' => 'too tired', 'C' => 'that', 'D' => 'she fell asleep immediately'],
        'key' => 'C',
    ],
    [
        'kind' => 'error',
        'q' => 'Each of the rooms (A) have (B) an air conditioner (C) and a telephone (D).',
        'options' => ['A' => 'Each of the rooms', 'B' => 'have', 'C' => 'an air conditioner', 'D' => 'and a telephone'],
        'key' => 'B',
    ],
];
