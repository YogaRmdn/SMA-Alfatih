<?php

/*
| Bank soal DUMMY Listening Comprehension — Paket A (50 butir).
|
| Dipakai ToeflDummySeeder untuk mengisi ujian demo TOEFL ITP Level 1.
| Semua butir adalah CONTOH latihan yang dibuat sendiri (bukan butir resmi
| ETS) dan dibebaskan dipakai untuk demo pengembangan.
|
| Struktur tiap butir:
|   script  — naskah percakapan/talk (ditulis di `question_text` karena
|             modul demo ini belum menyertakan berkas audio).
|   q       — pertanyaan.
|   options — pilihan jawaban A–D.
|   key     — kunci jawaban.
*/

return [
    /*
    | Bagian 1: percakapan pendek (item 1–30)
    */
    [
        'script' => "M: Excuse me, is this seat taken?\nW: No, it isn't. But you can't talk on your phone in here.",
        'q' => 'Where does this conversation most probably take place?',
        'options' => ['A' => 'At a restaurant', 'B' => 'In a library', 'C' => 'At a bus stop', 'D' => 'In a classroom'],
        'key' => 'B',
    ],
    [
        'script' => "M: I'd like to return this book, but I'm afraid it's two weeks overdue.\nW: That's all right. Just fill out this form and I'll waive the fine.",
        'q' => 'What does the woman tell the man to do?',
        'options' => ['A' => 'Apologize to the librarian', 'B' => 'Pay the fine immediately', 'C' => 'Complete a form', 'D' => 'Renew the book online'],
        'key' => 'C',
    ],
    [
        'script' => "W: Are you coming to the football match on Saturday?\nM: I'd love to, but I have to work at my father's shop.",
        'q' => 'What will the man probably do on Saturday?',
        'options' => ['A' => 'Watch the match at home', 'B' => "Work in his father's shop", 'C' => 'Play football with his friends', 'D' => 'Visit his brother'],
        'key' => 'B',
    ],
    [
        'script' => "M: How much is the ticket to the museum?\nW: It's five dollars for adults, but students pay half price.",
        'q' => 'How much does a student pay?',
        'options' => ['A' => 'Two dollars and fifty cents', 'B' => 'Five dollars', 'C' => 'Ten dollars', 'D' => 'Fifteen dollars'],
        'key' => 'A',
    ],
    [
        'script' => "W: The air conditioner in this room isn't working. It's so hot.\nM: Let me open the window. That should let some fresh air in.",
        'q' => 'What are the speakers probably doing?',
        'options' => ['A' => 'Repairing an air conditioner', 'B' => 'Complaining about the weather', 'C' => 'Trying to cool the room', 'D' => 'Preparing to leave the room'],
        'key' => 'C',
    ],
    [
        'script' => "M: Did you finish the report for Mr. Anderson?\nW: Almost. I just need to check the figures in the last section.",
        'q' => 'What does the woman mean?',
        'options' => ['A' => 'The report is completely finished', 'B' => 'She still has some work to do', 'C' => 'The figures are incorrect', 'D' => "She needs Mr. Anderson's help"],
        'key' => 'B',
    ],
    [
        'script' => "W: I'm thinking of taking a course in computer programming next semester.\nM: That's a good idea. It will look good on your resume.",
        'q' => "What does the man think about the woman's plan?",
        'options' => ['A' => 'He doubts it is useful', 'B' => 'He thinks it will benefit her', 'C' => 'He wants to take the same course', 'D' => 'He suggests she wait a year'],
        'key' => 'B',
    ],
    [
        'script' => "M: I'm sorry, but the tickets for tonight's concert are sold out.\nW: Oh no! We have been waiting for weeks.",
        'q' => 'How does the woman probably feel?',
        'options' => ['A' => 'Relieved', 'B' => 'Disappointed', 'C' => 'Confused', 'D' => 'Angry'],
        'key' => 'B',
    ],
    [
        'script' => "W: Could you give me a ride to the airport tomorrow morning?\nM: Sure, but I have to leave at six. Is that too early for you?",
        'q' => 'What time will the man pick the woman up?',
        'options' => ['A' => 'At five o’clock', 'B' => 'At six o’clock', 'C' => 'At half past six', 'D' => 'At seven o’clock'],
        'key' => 'B',
    ],
    [
        'script' => "M: How long have you been studying English?\nW: Since I was in elementary school — about eight years now.",
        'q' => 'When did the woman start learning English?',
        'options' => ['A' => 'When she was in elementary school', 'B' => 'Two years ago', 'C' => 'When she started university', 'D' => 'Eight months ago'],
        'key' => 'A',
    ],
    [
        'script' => "W: The bakery on Main Street is closing down. They say business has been bad.\nM: That's a shame. Their bread was the best in town.",
        'q' => 'What can be inferred about the bakery?',
        'options' => ['A' => 'It moved to another street', 'B' => 'It is going out of business', 'C' => 'It raised its prices', 'D' => 'It is looking for new workers'],
        'key' => 'B',
    ],
    [
        'script' => "M: Did you hear that the science fair has been postponed?\nW: Yes, until next month, because the gym is being repaired.",
        'q' => 'Why has the science fair been postponed?',
        'options' => ['A' => 'Few students entered', 'B' => 'The venue is being repaired', 'C' => 'The judges were unavailable', 'D' => 'There was not enough money'],
        'key' => 'B',
    ],
    [
        'script' => "W: Would you like some more rice?\nM: No, thank you. I'm full. That was an excellent meal.",
        'q' => 'What does the man imply?',
        'options' => ['A' => 'He did not enjoy the food', 'B' => 'He wants more rice', 'C' => 'He has eaten enough', 'D' => 'He is cooking dinner'],
        'key' => 'C',
    ],
    [
        'script' => "M: I need to exchange these shoes. They are a size too small.\nW: Sure. Do you have your receipt with you?",
        'q' => 'What is the woman probably doing?',
        'options' => ['A' => 'Shopping for shoes', 'B' => 'Working as a salesperson', 'C' => 'Teaching a class', 'D' => 'Tailoring clothes'],
        'key' => 'B',
    ],
    [
        'script' => "W: The weather forecast says it will rain this afternoon.\nM: Then let's move the picnic to Sunday.",
        'q' => 'What does the man suggest?',
        'options' => ['A' => 'Cancelling the picnic', 'B' => 'Having the picnic on Sunday', 'C' => 'Watching the forecast again', 'D' => 'Bringing umbrellas'],
        'key' => 'B',
    ],
    [
        'script' => "M: How did you do on the history test?\nW: I was sure I would fail, but I actually got the second-highest score.",
        'q' => 'What does the woman say about her test?',
        'options' => ['A' => 'She failed it', 'B' => 'She scored very well', 'C' => 'She did not take it', 'D' => 'She will take it again'],
        'key' => 'B',
    ],
    [
        'script' => "W: Excuse me, does this bus go to the train station?\nM: No, you will have to get off at the bridge and take the number twelve.",
        'q' => 'What must the woman do?',
        'options' => ['A' => 'Stay on the bus until the end of the route', 'B' => 'Change to another bus at the bridge', 'C' => 'Walk to the station', 'D' => 'Take a taxi instead'],
        'key' => 'B',
    ],
    [
        'script' => "M: I've decided to give up coffee for a month.\nW: Good luck. You will probably get headaches for the first few days.",
        'q' => 'What does the woman think will happen?',
        'options' => ['A' => 'The man will feel better immediately', 'B' => 'The man may suffer headaches at first', 'C' => 'The man will certainly fail', 'D' => 'Coffee is good for his health'],
        'key' => 'B',
    ],
    [
        'script' => "W: Are you going to the graduation ceremony next Friday?\nM: I wouldn't miss it for the world. My sister is receiving an award.",
        'q' => 'What does the man mean?',
        'options' => ['A' => 'He will definitely attend', 'B' => 'He is unable to attend', 'C' => 'He does not care about the ceremony', 'D' => 'He is giving a speech there'],
        'key' => 'A',
    ],
    [
        'script' => "M: The copy machine on the second floor is broken again.\nW: Again? I will call the maintenance department right away.",
        'q' => 'What will the woman probably do next?',
        'options' => ['A' => 'Repair the machine herself', 'B' => 'Report the problem to maintenance', 'C' => 'Order a new machine', 'D' => 'Move to the first floor'],
        'key' => 'B',
    ],
    [
        'script' => "W: How was your trip to Bandung?\nM: The scenery was beautiful, but the traffic was terrible.",
        'q' => 'What did the man think of his trip?',
        'options' => ['A' => 'He enjoyed only part of it', 'B' => 'He disliked it completely', 'C' => 'It was perfect', 'D' => 'It was too short'],
        'key' => 'A',
    ],
    [
        'script' => "M: Have you finished reading the novel I lent you?\nW: I've read up to the fifth chapter. I'll return it next week.",
        'q' => 'What does the woman mean?',
        'options' => ['A' => 'She has finished the novel', 'B' => 'She lost the novel', 'C' => 'She will return it soon', 'D' => 'She wants to keep it longer'],
        'key' => 'C',
    ],
    [
        'script' => "W: We still need two more people for the volleyball team.\nM: Why not ask David and Anita? They are both athletic.",
        'q' => 'What does the man suggest?',
        'options' => ['A' => 'Forming a second team', 'B' => 'Asking David and Anita to join', 'C' => 'Practising more often', 'D' => 'Cancelling the match'],
        'key' => 'B',
    ],
    [
        'script' => "M: I can't believe the semester is almost over. Where did the time go?\nW: Tell me about it. I still have three papers to write.",
        'q' => 'What does the woman mean?',
        'options' => ['A' => 'She agrees that time passed quickly', 'B' => 'She has finished her papers', 'C' => 'She wants a longer semester', 'D' => 'She enjoys writing papers'],
        'key' => 'A',
    ],
    [
        'script' => "W: Is this pen yours?\nM: No, mine is the blue one. This must belong to someone else.",
        'q' => 'What color is the man’s pen?',
        'options' => ['A' => 'Black', 'B' => 'Red', 'C' => 'Blue', 'D' => 'Green'],
        'key' => 'C',
    ],
    [
        'script' => "M: The new restaurant near the campus is quite expensive.\nW: Yes, but the food is worth every penny.",
        'q' => 'What does the woman think about the restaurant?',
        'options' => ['A' => 'It is overpriced', 'B' => 'Its food justifies the price', 'C' => 'It should close down', 'D' => 'It serves very small portions'],
        'key' => 'B',
    ],
    [
        'script' => "W: Did you remember to bring the documents for the meeting?\nM: Oh no, I left them on my desk at home.",
        'q' => 'What happened to the documents?',
        'options' => ['A' => 'They were given to the manager', 'B' => 'They are at the man’s home', 'C' => 'They were lost on the way', 'D' => 'They are in the car'],
        'key' => 'B',
    ],
    [
        'script' => "M: How many students are in your English class?\nW: There were thirty at the beginning of the year, but five have moved away.",
        'q' => 'How many students are in the class now?',
        'options' => ['A' => 'Twenty', 'B' => 'Twenty-five', 'C' => 'Thirty', 'D' => 'Thirty-five'],
        'key' => 'B',
    ],
    [
        'script' => "W: Would you like to see a movie tonight?\nM: I'd rather stay home. I have an early flight tomorrow.",
        'q' => 'What will the man probably do?',
        'options' => ['A' => 'Go to the cinema', 'B' => 'Stay at home', 'C' => 'Take a late flight', 'D' => 'Visit a friend'],
        'key' => 'B',
    ],
    [
        'script' => "M: Congratulations on winning the speech contest!\nW: Thanks. I couldn't have done it without my teacher's help.",
        'q' => 'What does the woman imply?',
        'options' => ['A' => 'She won without any help', 'B' => 'Her teacher helped her a great deal', 'C' => 'She will enter another contest', 'D' => 'Her teacher won the contest'],
        'key' => 'B',
    ],

    /*
    | Bagian 2: percakapan panjang (item 31–40)
    */
    [
        'script' => "W: Good morning, Student Services. How can I help you?\nM: Hello. I'd like to know how to apply for a scholarship.\nW: Sure. First, you need a minimum grade point average of 3.5, and then you fill out the form available on our website.\nM: Is there a deadline?\nW: Yes, applications close on the fifteenth of next month.",
        'q' => "What is the man's main purpose?",
        'options' => ['A' => 'To register for a course', 'B' => 'To apply for a scholarship', 'C' => 'To check his grades', 'D' => 'To pick up a printed form'],
        'key' => 'B',
    ],
    [
        'script' => "M: Hi, Ana. Are you still working at the bookstore?\nW: Not anymore. I got a job at the hospital last month.\nM: That's great. Do you like it there?\nW: I do. The hours are longer, but the pay is much better.",
        'q' => 'Where does Ana work now?',
        'options' => ['A' => 'At a bookstore', 'B' => 'At a hospital', 'C' => 'At a library', 'D' => 'At a school'],
        'key' => 'B',
    ],
    [
        'script' => "W: Have you decided where to go for the holiday?\nM: We were thinking of the mountains, but the forecast warns of snow.\nW: Why not go to the beach instead? The weather should be lovely.\nM: That's not a bad idea. Let's check the hotel prices.",
        'q' => 'What are the speakers discussing?',
        'options' => ['A' => 'Where to spend their holiday', 'B' => 'What to pack for a trip', 'C' => 'How to reach the mountains', 'D' => 'When to leave home'],
        'key' => 'A',
    ],
    [
        'script' => "M: Professor, may I ask about the final project?\nW: Of course. What would you like to know?\nM: Can I work with a partner?\nW: Yes, but each of you must submit an individual report.",
        'q' => 'What must the students do?',
        'options' => ['A' => 'Submit only one report for both students', 'B' => 'Write separate reports', 'C' => 'Choose their topic together', 'D' => 'Present the project in class'],
        'key' => 'B',
    ],
    [
        'script' => "W: I heard the library is staying open later during exams.\nM: That's right. From Sunday it will close at midnight instead of ten.\nW: That's helpful. I usually study there in the evening.\nM: Just remember to bring your student card, or you won't be allowed in.",
        'q' => 'Until what time will the library be open?',
        'options' => ['A' => 'Ten o’clock', 'B' => 'Eleven o’clock', 'C' => 'Midnight', 'D' => 'Two o’clock in the morning'],
        'key' => 'C',
    ],
    [
        'script' => "M: How was your first week at the new school?\nW: Exhausting, but I like my teachers. The science lab is especially well equipped.\nM: Do you miss your old friends?\nW: Sometimes, but I've already made several new ones.",
        'q' => 'How does the woman feel about her new school?',
        'options' => ['A' => 'She dislikes it', 'B' => 'She misses her old friends too much', 'C' => 'She has adapted well', 'D' => 'She is afraid of her teachers'],
        'key' => 'C',
    ],
    [
        'script' => "W: Excuse me, I bought this shirt here yesterday and it has a stain on the sleeve.\nM: I'm sorry about that. Do you have the receipt?\nW: Yes, here it is.\nM: In that case, you can exchange it for another one, or take a refund.",
        'q' => 'What can the woman do with the shirt?',
        'options' => ['A' => 'Exchange it or get her money back', 'B' => 'Only exchange it', 'C' => 'Get a free repair', 'D' => 'Keep it at a discount'],
        'key' => 'A',
    ],
    [
        'script' => "W: Are you going to the career fair on Thursday?\nM: I am. Several companies will be there, and I want to hand in my resume.\nW: Which companies are you most interested in?\nM: The engineering firms, mainly. I'd like an internship this summer.",
        'q' => 'What does the man want from the career fair?',
        'options' => ['A' => 'A part-time job', 'B' => 'An internship offer', 'C' => 'Advice on writing a resume', 'D' => 'Information about engineering courses'],
        'key' => 'B',
    ],
    [
        'script' => "W: Did you see the email about the schedule change?\nM: No. What happened?\nW: The meeting has been moved from Monday to Wednesday morning.\nM: Thanks for telling me. I'll update my calendar.",
        'q' => 'When will the meeting now take place?',
        'options' => ['A' => 'On Monday morning', 'B' => 'On Wednesday morning', 'C' => 'On Wednesday afternoon', 'D' => 'On Friday morning'],
        'key' => 'B',
    ],
    [
        'script' => "M: I'm thinking about buying a bicycle to ride to class.\nW: That's a good idea, but make sure you lock it. Several bikes have been stolen on campus.\nM: I'll buy a strong chain. Where can I park it safely?\nW: There's a covered rack behind the science building.",
        'q' => 'Where does the woman suggest parking the bicycle?',
        'options' => ['A' => 'Near the library', 'B' => 'Behind the science building', 'C' => 'In front of the classroom', 'D' => 'Next to the dormitory'],
        'key' => 'B',
    ],

    /*
    | Bagian 3: monolog/talk (item 41–50)
    */
    [
        'script' => 'Welcome to our summer program. The program runs for six weeks, from June fifteenth to July twenty-fifth. Classes are held every weekday morning from eight to eleven. In the afternoons you may join optional activities such as swimming, painting, or computer workshops. The fee is three hundred dollars, and lunch is included.',
        'q' => 'What is the main purpose of the talk?',
        'options' => ['A' => 'To describe a summer program', 'B' => 'To introduce three teachers', 'C' => 'To explain how to swim', 'D' => 'To announce a school holiday'],
        'key' => 'A',
    ],
    [
        'script' => 'Attention, passengers. Flight GA 302 to Surabaya has been delayed due to bad weather. The new departure time is two thirty. Passengers may collect meal vouchers at counter twelve. We apologize for the inconvenience.',
        'q' => 'Why was the flight delayed?',
        'options' => ['A' => 'A technical problem', 'B' => 'Bad weather', 'C' => 'A late crew', 'D' => 'A staff strike'],
        'key' => 'B',
    ],
    [
        'script' => 'The orangutan is found only on the islands of Borneo and Sumatra. These great apes spend most of their lives in trees, where they build a sleeping nest each night. Their diet consists mainly of fruit, though they also eat leaves and bark. Because large areas of forest are being cut down, orangutans are now an endangered species.',
        'q' => 'What is the talk mainly about?',
        'options' => ['A' => 'The diet of forest birds', 'B' => 'The orangutan and its habitat', 'C' => 'How to build a tree house', 'D' => 'Why trees are cut down'],
        'key' => 'B',
    ],
    [
        'script' => 'Good afternoon. This is a reminder that the school clinic will be closed tomorrow for routine disinfection. Students who need medical attention should go to the health center on Jalan Merdeka. The clinic will reopen on Friday at seven thirty in the morning.',
        'q' => 'What will happen to the school clinic tomorrow?',
        'options' => ['A' => 'It will give away free medicine', 'B' => 'It will be closed', 'C' => 'It will move to a new building', 'D' => 'It will extend its opening hours'],
        'key' => 'B',
    ],
    [
        'script' => 'Volcanoes are among the most powerful forces of nature. When a volcano erupts, ash and gas are thrown high into the air, and rivers of lava flow down its slopes. Although eruptions can destroy villages and farmland, volcanic ash eventually breaks down into soil that is remarkably fertile. For this reason, many farmers choose to live near active volcanoes.',
        'q' => 'Why do some farmers live near active volcanoes?',
        'options' => ['A' => 'The land is inexpensive', 'B' => 'The soil becomes very fertile', 'C' => 'Ash protects the crops', 'D' => 'Lava warms the fields'],
        'key' => 'B',
    ],
    [
        'script' => "Before the invention of the printing press, books were copied by hand, usually by monks in monasteries. A single book could take a year to produce, so books were rare and expensive. After Gutenberg's press was introduced in the fifteenth century, books could be printed quickly and cheaply. Literacy spread, and learning was no longer limited to the wealthy.",
        'q' => 'What is the main idea of the talk?',
        'options' => ['A' => 'Monks enjoyed copying books', 'B' => 'Printing made books cheap and learning widespread', 'C' => 'Books have always been expensive', 'D' => 'Literacy declined in the fifteenth century'],
        'key' => 'B',
    ],
    [
        'script' => 'Our school library has changed its borrowing rules. Students may now borrow up to four books at a time instead of two. The loan period remains two weeks, but it may be extended once. Reference books and magazines, however, must be read inside the library.',
        'q' => 'How many books can a student borrow now?',
        'options' => ['A' => 'Two', 'B' => 'Three', 'C' => 'Four', 'D' => 'Six'],
        'key' => 'C',
    ],
    [
        'script' => 'Sleep is essential for good health. During deep sleep, the body repairs tissue and strengthens the immune system. Teenagers in particular need about eight to ten hours of sleep each night. Yet many teenagers stay up late using their phones, and as a result they often struggle to concentrate in class.',
        'q' => 'What problem do many teenagers face according to the talk?',
        'options' => ['A' => 'Lack of sleep that affects concentration', 'B' => 'Too much exercise', 'C' => 'Difficulty reading books', 'D' => 'Weak immune systems in winter only'],
        'key' => 'A',
    ],
    [
        'script' => 'Attention all students: the annual science exhibition will be held in the school hall next Friday. Each class has prepared a display, and judges will select the three best projects. Visitors are welcome from nine in the morning until two in the afternoon. Please turn off your mobile phones inside the hall.',
        'q' => 'Who will choose the winning projects?',
        'options' => ['A' => 'The class teachers', 'B' => 'A panel of judges', 'C' => 'The students themselves', 'D' => 'The principal'],
        'key' => 'B',
    ],
    [
        'script' => 'The coral polyp is a tiny animal that lives in colonies on the ocean floor. By secreting a hard skeleton of calcium carbonate, polyps build the structures we know as coral reefs. Reefs provide food and shelter for thousands of species of fish. Unfortunately, rising ocean temperatures cause corals to expel the algae that keep them alive, a process known as bleaching.',
        'q' => 'What causes coral bleaching according to the talk?',
        'options' => ['A' => 'Pollution from ships', 'B' => 'Rising ocean temperatures', 'C' => 'Too many fish', 'D' => 'A shortage of calcium'],
        'key' => 'B',
    ],
];
