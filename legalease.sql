-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 15, 2026 at 10:03 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `legalease`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookmarks`
--

CREATE TABLE `bookmarks` (
  `bookmark_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `legal_document_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cases`
--

CREATE TABLE `cases` (
  `cid` int(11) NOT NULL,
  `applicant` varchar(255) DEFAULT NULL,
  `defendent` varchar(255) DEFAULT NULL,
  `body` text DEFAULT NULL,
  `client_id` int(11) NOT NULL,
  `status` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `judge` varchar(255) DEFAULT NULL,
  `case_info` int(11) DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cases`
--

INSERT INTO `cases` (`cid`, `applicant`, `defendent`, `body`, `client_id`, `status`, `created_at`, `judge`, `case_info`, `owner_id`) VALUES
(2, 'Kesavananda Bharati', 'State of Kerala', '1973 | Supreme Court | Established the Basic Structure Doctrine.', 2, 'Landmark', '2026-11-15 07:50:51', 'S.M. Sikri, J.M. Shelat, K.S. Hegde, A.N. Grover, A.N. Ray, P. Jaganmohan Reddy, D.G. Palekar, H.R. Khanna, K.K. Mathew, M.H. Beg, S.N. Dwivedi, A.K. Mukherjea, Y.V. Chandrachud', NULL, NULL),
(3, 'Maneka Gandhi', 'Union of India', '1978 | Supreme Court | Expanded Article 21 and established that legal procedures must be fair, just and reasonable.', 2, 'Landmark', '2026-11-15 07:50:51', 'M.H. Beg, Y.V. Chandrachud, V.R. Krishna Iyer, P.N. Bhagwati, N.L. Untwalia, S. Murtaza Fazal Ali, P.S. Kailasam', NULL, NULL),
(4, 'I.C. Golaknath', 'State of Punjab', '1967 | Supreme Court | Held that Parliament could not amend Fundamental Rights. This position was later modified by constitutional developments.', 2, 'Landmark', '2026-11-15 07:50:51', 'K. Subba Rao, J.C. Shah, S.M. Sikri, J.M. Shelat, C.A. Vaidialingam, R.S. Bachawat, V. Ramaswami, K.N. Wanchoo, M. Hidayatullah, R.S. Mudholkar, K.S. Hegde', NULL, NULL),
(5, 'Minerva Mills Ltd.', 'Union of India', '1980 | Supreme Court | Reaffirmed the Basic Structure Doctrine and limited amending power.', 2, 'Landmark', '2026-11-15 07:50:51', 'Y.V. Chandrachud, A.C. Gupta, N.L. Untwalia, P.S. Kailasam, P.N. Bhagwati', NULL, NULL),
(6, 'I.R. Coelho', 'State of Tamil Nadu', '2007 | Supreme Court | Held that Ninth Schedule laws inserted after 24 April 1973 are subject to basic structure review.', 2, 'Landmark', '2026-11-15 07:50:51', 'Y.K. Sabharwal, K.G. Balakrishnan, S.P. Bharucha, Arijit Pasayat, C.K. Thakker, P.P. Naolekar, Altamas Kabir, S.H. Kapadia, D.K. Jain', NULL, NULL),
(7, 'S.R. Bommai', 'Union of India', '1994 | Supreme Court | Established principles governing Article 356 and judicial review of President’s Rule.', 2, 'Landmark', '2026-11-15 07:50:51', 'M.N. Venkatachaliah, A.M. Ahmadi, Kuldip Singh, P.B. Sawant, K. Ramaswamy, J.S. Verma, S.C. Agrawal, Yogeshwar Dayal, B.P. Jeevan Reddy', NULL, NULL),
(8, 'Indra Sawhney', 'Union of India', '1992 | Supreme Court | Upheld 27% OBC reservation in public employment and introduced the creamy layer principle.', 2, 'Landmark', '2026-11-15 07:50:51', 'M.H. Kania, M.N. Venkatachaliah, S.C. Agrawal, Kuldip Singh, P.B. Sawant, T.K. Thommen, R.M. Sahai, S. Ratnavel Pandian, A.M. Ahmadi', NULL, NULL),
(9, 'Vishaka', 'State of Rajasthan', '1997 | Supreme Court | Established guidelines against sexual harassment at the workplace.', 2, 'Landmark', '2026-11-15 07:50:51', 'J.S. Verma, Sujata V. Manohar, B.N. Kirpal', NULL, NULL),
(10, 'NALSA', 'Union of India', '2014 | Supreme Court | Recognised transgender persons as a third gender and affirmed their constitutional rights.', 2, 'Landmark', '2026-11-15 07:50:51', NULL, NULL, NULL),
(11, 'Navtej Singh Johar', 'Union of India', '2018 | Supreme Court | Decriminalised consensual same-sex relations between adults by reading down Section 377 IPC.', 2, 'Landmark', '2026-11-15 07:50:51', 'Dipak Misra, R.F. Nariman, A.M. Khanwilkar, D.Y. Chandrachud, Indu Malhotra', NULL, NULL),
(12, 'Joseph Shine', 'Union of India', '2018 | Supreme Court | Struck down Section 497 IPC, decriminalising adultery.', 2, 'Landmark', '2026-11-15 07:50:51', 'Dipak Misra, A.K. Sikri, A.M. Khanwilkar, D.Y. Chandrachud, Indu Malhotra', NULL, NULL),
(13, 'Justice K.S. Puttaswamy', 'Union of India', '2017 | Supreme Court | Recognised the right to privacy as a Fundamental Right.', 2, 'Landmark', '2026-11-15 07:50:51', 'J.S. Khehar, J. Chelameswar, S.A. Bobde, R.K. Agrawal, R.F. Nariman, A.M. Sapre, D.Y. Chandrachud, Sanjay Kishan Kaul, S. Abdul Nazeer', NULL, NULL),
(14, 'Shreya Singhal', 'Union of India', '2015 | Supreme Court | Struck down Section 66A of the IT Act for violating freedom of speech.', 2, 'Landmark', '2026-11-15 07:50:51', 'J. Chelameswar, R.F. Nariman', NULL, NULL),
(15, 'ADM Jabalpur', 'Shivkant Shukla', '1976 | Supreme Court | Emergency-era judgment restricting habeas corpus remedies; subsequently repudiated and overruled.', 2, 'Landmark', '2026-11-15 07:50:51', 'A.N. Ray, H.R. Khanna, M.H. Beg, Y.V. Chandrachud, P.N. Bhagwati', NULL, NULL),
(16, 'Bachan Singh', 'State of Punjab', '1980 | Supreme Court | Established the rarest of rare doctrine for the death penalty.', 2, 'Landmark', '2026-11-15 07:50:51', 'Y.V. Chandrachud, A.C. Gupta, N.L. Untwalia, P.N. Bhagwati, R.S. Pathak', NULL, NULL),
(17, 'Mithu', 'State of Punjab', '1983 | Supreme Court | Struck down mandatory death penalty under Section 303 IPC.', 2, 'Landmark', '2026-11-15 07:50:51', 'Y.V. Chandrachud, D.A. Desai, R.B. Misra, O. Chinnappa Reddy, A.P. Sen', NULL, NULL),
(18, 'D.K. Basu', 'State of West Bengal', '1997 | Supreme Court | Established safeguards against custodial violence and illegal arrest.', 2, 'Landmark', '2026-11-15 07:50:51', 'A.S. Anand, K. Venkataswami', NULL, NULL),
(19, 'Hussainara Khatoon', 'State of Bihar', '1979 | Supreme Court | Recognised speedy trial as a component of Article 21.', 2, 'Landmark', '2026-11-15 07:50:51', 'P.N. Bhagwati, D.A. Desai', NULL, NULL),
(20, 'Olga Tellis', 'Bombay Municipal Corporation', '1985 | Supreme Court | Connected the right to livelihood with the right to life under Article 21.', 2, 'Landmark', '2026-11-15 07:50:51', 'Y.V. Chandrachud, A.N. Sen, E.S. Venkataramiah, R.B. Misra, V. Balakrishna Eradi', NULL, NULL),
(21, 'M.C. Mehta', 'Union of India', '1987 | Supreme Court | Established the principle of absolute liability in the Oleum Gas Leak case.', 2, 'Landmark', '2026-11-15 07:50:51', 'P.N. Bhagwati, Ranganath Misra, G.L. Oza, M.M. Dutt, K.N. Singh', NULL, NULL),
(22, 'Vellore Citizens Welfare Forum', 'Union of India', '1996 | Supreme Court | Recognised the precautionary principle and polluter pays principle.', 2, 'Landmark', '2026-11-15 07:50:51', 'Kuldip Singh, S. Saghir Ahmad', NULL, NULL),
(23, 'T.N. Godavarman Thirumulpad', 'Union of India', '1996 | Supreme Court | Expanded the interpretation of forests under the Forest Conservation Act.', 2, 'Landmark', '2026-11-15 07:50:51', 'Kuldip Singh, S. Saghir Ahmad', NULL, NULL),
(24, 'Subhash Kumar', 'State of Bihar', '1991 | Supreme Court | Recognised pollution-free water and air as part of Article 21.', 2, 'Landmark', '2026-11-15 07:50:51', 'S. Mukharji, K.N. Singh', NULL, NULL),
(25, 'Lalita Kumari', 'Government of Uttar Pradesh', '2013 | Supreme Court | Held that FIR registration is mandatory when information discloses a cognisable offence, subject to limited exceptions.', 2, 'Landmark', '2026-11-15 07:50:51', 'P. Sathasivam, Ranjana Prakash Desai', NULL, NULL),
(26, 'Arnesh Kumar', 'State of Bihar', '2014 | Supreme Court | Issued safeguards against unnecessary arrests.', 2, 'Landmark', '2026-11-15 07:50:51', 'Chandramauli Kr. Prasad, Pinaki Chandra Ghose', NULL, NULL),
(27, 'Lily Thomas', 'Union of India', '2013 | Supreme Court | Held that qualifying criminal convictions lead to immediate disqualification of sitting legislators.', 2, 'Landmark', '2026-11-15 07:50:51', 'Aftab Alam, Ranjana Prakash Desai', NULL, NULL),
(28, 'Sarla Mudgal', 'Union of India', '1995 | Supreme Court | Examined conversion and bigamy under personal laws.', 2, 'Landmark', '2026-11-15 07:50:51', 'Kuldip Singh, R.M. Sahai', NULL, NULL),
(29, 'Shayara Bano', 'Union of India', '2017 | Supreme Court | Invalidated instant triple talaq.', 2, 'Landmark', '2026-11-15 07:50:51', 'J.S. Khehar, Kurian Joseph, R.F. Nariman, U.U. Lalit, Abdul Nazeer', NULL, NULL),
(30, 'Shafin Jahan', 'Asokan K.M.', '2018 | Supreme Court | Affirmed an adult individual’s autonomy in choosing a spouse.', 2, 'Landmark', '2026-11-15 07:50:51', 'Dipak Misra, A.M. Khanwilkar', NULL, NULL),
(31, 'K.S. Puttaswamy (Aadhaar)', 'Union of India', '2018 | Supreme Court | Examined Aadhaar constitutionality and upheld the framework subject to limitations.', 2, 'Landmark', '2026-11-15 07:50:51', 'Dipak Misra, A.K. Sikri, A.M. Khanwilkar, D.Y. Chandrachud, Ashok Bhushan', NULL, NULL),
(32, 'Anuradha Bhasin', 'Union of India', '2020 | Supreme Court | Held that freedom of speech and trade through the internet receives constitutional protection.', 2, 'Landmark', '2026-11-15 07:50:51', 'N.V. Ramana, R. Subhash Reddy, B.R. Gavai', NULL, NULL),
(33, 'Common Cause', 'Union of India', '2018 | Supreme Court | Recognised passive euthanasia and advance directives subject to safeguards.', 2, 'Landmark', '2026-11-15 07:50:51', 'Dipak Misra, A.M. Khanwilkar, A.K. Sikri, D.Y. Chandrachud, Ashok Bhushan', NULL, NULL),
(34, 'P.A. Inamdar', 'State of Maharashtra', '2005 | Supreme Court | Examined reservation and admission rights in private unaided professional institutions.', 2, 'Landmark', '2026-11-15 07:50:51', 'R.C. Lahoti, D.M. Dharmadhikari, P.K. Balasubramanyan, S.B. Sinha, Arun Kumar', NULL, NULL),
(35, 'T.M.A. Pai Foundation', 'State of Karnataka', '2002 | Supreme Court | Clarified rights of minority and private educational institutions.', 2, 'Landmark', '2026-11-15 07:50:51', 'B.N. Kirpal, R.C. Lahoti, N. Santosh Hegde, S.N. Variava, Ashok Bhan, S.B. Sinha, Ruma Pal', NULL, NULL),
(36, 'Unni Krishnan', 'State of Andhra Pradesh', '1993 | Supreme Court | Recognised the right to education for children up to 14 years under Article 21.', 2, 'Landmark', '2026-11-15 07:50:51', 'M.N. Venkatachaliah, S. Ratnavel Pandian, A.M. Ahmadi, P.B. Sawant, K. Ramaswamy', NULL, NULL),
(37, 'Mohini Jain', 'State of Karnataka', '1992 | Supreme Court | Connected the right to education with the right to life.', 2, 'Landmark', '2026-11-15 07:50:51', 'L.M. Sharma, S.C. Agrawal', NULL, NULL),
(38, 'M.C. Mehta (Taj Trapezium)', 'Union of India', '1996 | Supreme Court | Addressed industrial pollution affecting the Taj Mahal.', 2, 'Landmark', '2026-11-15 07:50:51', NULL, NULL, NULL),
(39, 'Indian Council for Enviro-Legal Action', 'Union of India', '1996 | Supreme Court | Applied the polluter pays principle to environmental restoration.', 2, 'Landmark', '2026-11-15 07:50:51', 'S.C. Agrawal, G.B. Pattanaik', NULL, NULL),
(40, 'Rural Litigation and Entitlement Kendra', 'State of Uttar Pradesh', '1985 | Supreme Court | Addressed environmental damage caused by limestone quarrying in the Doon Valley.', 2, 'Landmark', '2026-11-15 07:50:51', 'P.N. Bhagwati, R.S. Pathak', NULL, NULL),
(41, 'Bandhua Mukti Morcha', 'Union of India', '1984 | Supreme Court | Strengthened protections against bonded labour and promoted PIL.', 2, 'Landmark', '2026-11-15 07:50:51', 'P.N. Bhagwati, R.S. Pathak', NULL, NULL),
(42, 'PUCL (Telephone Tapping)', 'Union of India', '1997 | Supreme Court | Established procedural safeguards for telephone interception.', 2, 'Landmark', '2026-11-15 07:50:51', NULL, NULL, NULL),
(43, 'PUCL (NOTA)', 'Union of India', '2013 | Supreme Court | Directed the introduction of NOTA in electronic voting machines.', 2, 'Landmark', '2026-11-15 07:50:51', NULL, NULL, NULL),
(44, 'L. Chandra Kumar', 'Union of India', '1997 | Supreme Court | Held that judicial review by High Courts and Supreme Court is part of the Basic Structure.', 2, 'Landmark', '2026-11-15 07:50:51', 'A.M. Ahmadi, S.P. Bharucha, M.M. Punchhi, K.T. Thomas, S.C. Sen, S. Saghir Ahmad, K. Venkataswami', NULL, NULL),
(45, 'Kihoto Hollohan', 'Zachillhu', '1992 | Supreme Court | Upheld the Tenth Schedule and subjected Speaker decisions to judicial review.', 2, 'Landmark', '2026-11-15 07:50:51', 'M.N. Venkatachaliah, S.C. Agrawal, K. Jayachandra Reddy, S.C. Sen', NULL, NULL),
(46, 'Raja Ram Pal', 'Honourable Speaker, Lok Sabha', '2007 | Supreme Court | Held that parliamentary privileges are subject to limited judicial review.', 2, 'Landmark', '2026-11-15 07:50:51', 'Y.K. Sabharwal, K.G. Balakrishnan, S.H. Kapadia, C.K. Thakker, Altamas Kabir', NULL, NULL),
(47, 'R.C. Cooper', 'Union of India', '1970 | Supreme Court | Bank Nationalisation case examining the impact of state action on Fundamental Rights.', 2, 'Landmark', '2026-11-15 07:50:51', 'S.M. Sikri, J.M. Shelat, K.S. Hegde, A.N. Grover, A.N. Ray, P. Jaganmohan Reddy, D.G. Palekar, H.R. Khanna, A.K. Mukherjea, Y.V. Chandrachud, M.H. Beg', NULL, NULL),
(48, 'Bennett Coleman & Co.', 'Union of India', '1972 | Supreme Court | Protected freedom of the press against restrictive newsprint policy.', 2, 'Landmark', '2026-11-15 07:50:51', 'A.N. Ray, K.K. Mathew, M.H. Beg, P. Jaganmohan Reddy, D.G. Palekar', NULL, NULL),
(49, 'Romesh Thappar', 'State of Madras', '1950 | Supreme Court | Established early constitutional protection of press freedom under Article 19(1)(a).', 2, 'Landmark', '2026-11-15 07:50:51', 'Patanjali Sastri, Fazl Ali, Mehr Chand Mahajan, B.K. Mukherjea, S.R. Das', NULL, NULL),
(50, 'State of Madras', 'Champakam Dorairajan', '1951 | Supreme Court | Struck down communal reservations in educational admissions, prompting the First Constitutional Amendment.', 2, 'Landmark', '2026-11-15 07:50:51', 'H.J. Kania, Fazl Ali, Patanjali Sastri, B.K. Mukherjea, S.R. Das', NULL, NULL),
(51, 'A.K. Gopalan', 'State of Madras', '1950 | Supreme Court | Adopted a narrow interpretation of personal liberty under Article 21, later departed from in subsequent judgments.', 2, 'Landmark', '2026-11-15 07:50:51', 'H.J. Kania, Fazl Ali, Patanjali Sastri, B.K. Mukherjea, S.R. Das', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `case_info`
--

CREATE TABLE `case_info` (
  `i_id` int(11) NOT NULL,
  `data` text DEFAULT NULL,
  `case_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `case_tasks`
--

CREATE TABLE `case_tasks` (
  `task_id` int(11) NOT NULL,
  `case_id` int(11) NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `priority` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `deadline` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `client_id` int(11) NOT NULL,
  `practitioner_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `phone` varchar(10) DEFAULT NULL,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`client_id`, `practitioner_id`, `name`, `phone`, `note`) VALUES
(2, NULL, 'Public Legal Library', NULL, 'Shared collection of landmark Indian court judgments');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `d_id` int(11) NOT NULL,
  `path` varchar(255) DEFAULT NULL,
  `case_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hearing`
--

CREATE TABLE `hearing` (
  `h_id` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `conclusion` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `d_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `c_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `legal_documents`
--

CREATE TABLE `legal_documents` (
  `legal_document_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `document_type` varchar(255) DEFAULT NULL,
  `court` varchar(255) DEFAULT NULL,
  `judge` varchar(255) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `act_name` varchar(255) DEFAULT NULL,
  `section` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `d_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `id` int(11) NOT NULL,
  `case_id` int(11) NOT NULL,
  `data` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`id`, `case_id`, `data`) VALUES
(1, 2, 'The Kesavananda Bharati case concerned the constitutional validity of land reform legislation in Kerala and the extent of Parliament’s power to amend the Constitution. The petitioner challenged provisions affecting property rights and the constitutional amendments that protected such legislation from judicial review.\r\n\r\nThe Supreme Court held that Parliament can amend any part of the Constitution under Article 368, including Fundamental Rights, but cannot destroy its Basic Structure. This judgment established the Basic Structure Doctrine, which remains a major limitation on constitutional amendment powers.'),
(2, 3, 'Maneka Gandhi challenged the decision of the government to impound her passport without providing adequate reasons or an opportunity to be heard. The case raised questions regarding personal liberty, procedural fairness and the relationship between Articles 14, 19 and 21.\r\n\r\nThe Supreme Court held that any procedure depriving a person of life or personal liberty must be fair, just and reasonable rather than arbitrary. The judgment gave a wider meaning to Article 21 and strengthened constitutional protection against arbitrary state action.'),
(3, 4, 'In I.C. Golaknath, the petitioners challenged constitutional amendments that affected their Fundamental Rights, particularly rights connected with property. The central question was whether Parliament could use its constituent power to amend Fundamental Rights.\r\n\r\nThe Supreme Court held that Parliament could not amend Fundamental Rights under the then interpretation of Article 368. It applied the doctrine of prospective overruling. This position was later modified by the Twenty-fourth Constitutional Amendment and the Basic Structure ruling in Kesavananda Bharati.'),
(4, 5, 'Minerva Mills challenged constitutional amendments that attempted to give Parliament extensive power to amend the Constitution and restrict judicial review. The dispute raised questions about the relationship between Fundamental Rights and Directive Principles of State Policy.\r\n\r\nThe Supreme Court struck down the relevant provisions of the Forty-second Constitutional Amendment. It held that limited amending power and judicial review are part of the Basic Structure of the Constitution. The judgment reaffirmed that Parliament cannot use its amendment power to remove constitutional limitations on itself.'),
(5, 6, 'I.R. Coelho concerned the validity of laws placed in the Ninth Schedule of the Constitution. The main issue was whether such laws could escape judicial review merely because they were included in the Ninth Schedule.\r\n\r\nThe Supreme Court held that laws inserted into the Ninth Schedule after 24 April 1973 can be reviewed if they damage the Basic Structure of the Constitution. The judgment clarified that constitutional protection cannot be used to completely exclude judicial scrutiny of legislation affecting Fundamental Rights.'),
(6, 7, 'S.R. Bommai challenged the dismissal of state governments and the use of President’s Rule under Article 356. The case raised questions about federalism, the role of Governors and the limits of executive power in dismissing elected state governments.\r\n\r\nThe Supreme Court held that the proclamation of President’s Rule is subject to judicial review. It also stated that a government majority should ordinarily be tested on the floor of the legislative assembly. The judgment placed important constitutional limitations on the misuse of Article 356.'),
(7, 8, 'Indra Sawhney challenged the government decision to implement reservations for Other Backward Classes in public employment following the Mandal Commission recommendations. The case examined the constitutional validity and limits of reservation under Articles 14, 15 and 16.\r\n\r\nThe Supreme Court upheld 27 percent reservation for OBCs in public employment and introduced the creamy layer principle to exclude socially advanced members of backward classes. It also generally established a 50 percent ceiling on reservations, subject to exceptional circumstances.'),
(8, 9, 'Vishaka arose after the gang rape of a social worker in Rajasthan and highlighted the absence of specific legislation addressing sexual harassment at workplaces. The petitioners approached the Supreme Court seeking protection of working women and recognition of their constitutional rights.\r\n\r\nThe Supreme Court laid down the Vishaka Guidelines to prevent and address sexual harassment at workplaces. The guidelines treated workplace safety as part of the rights to equality, life and occupation. They remained applicable until the enactment of the Sexual Harassment of Women at Workplace Act, 2013.'),
(9, 10, 'The National Legal Services Authority filed a petition seeking recognition and protection of the rights of transgender persons in India. The case examined gender identity, discrimination and the constitutional rights of individuals who did not identify within the traditional male-female classification.\r\n\r\nThe Supreme Court recognised transgender persons as a third gender and affirmed their right to self-identify their gender. It directed governments to take steps concerning education, employment, healthcare and social welfare. The judgment became a major constitutional recognition of transgender rights.'),
(10, 11, 'Navtej Singh Johar challenged the constitutional validity of Section 377 of the Indian Penal Code, which criminalised certain sexual acts. The petition argued that criminalisation of consensual same-sex relationships violated dignity, equality, privacy and personal liberty.\r\n\r\nThe Supreme Court read down Section 377 to the extent that it criminalised consensual sexual relations between adults. The judgment recognised individual autonomy, dignity and equality irrespective of sexual orientation. It marked a major development in constitutional protection of individual liberty.'),
(11, 12, 'Joseph Shine challenged Section 497 of the Indian Penal Code, which criminalised adultery and treated men and women differently under the law. The case raised questions about equality, privacy, dignity and the autonomy of individuals within marriage.\r\n\r\nThe Supreme Court unanimously struck down Section 497 as unconstitutional. It held that the provision violated Articles 14, 15 and 21 by treating women as subordinate to men in marital relationships. The judgment also clarified that adultery may remain a ground for civil matrimonial proceedings.'),
(12, 13, 'Justice K.S. Puttaswamy filed a petition questioning the constitutional validity of the Aadhaar scheme. The proceedings raised a larger question regarding whether the Constitution protects an individual right to privacy as part of Fundamental Rights.\r\n\r\nA nine-judge bench of the Supreme Court unanimously recognised privacy as a Fundamental Right under the Constitution. The judgment connected privacy with dignity, liberty and personal autonomy under Article 21 and other constitutional freedoms. It became the foundation for later judicial review of laws involving personal information and surveillance.'),
(13, 14, 'Shreya Singhal challenged Section 66A of the Information Technology Act, 2000, which criminalised sending certain offensive or annoying messages through electronic communication. The petition followed arrests made for online comments and criticism.\r\n\r\nThe Supreme Court struck down Section 66A as unconstitutional because its vague and broad language restricted freedom of speech under Article 19(1)(a). The Court distinguished discussion, advocacy and incitement, holding that restrictions on speech must satisfy constitutional standards. The judgment strengthened protection for online expression.'),
(14, 15, 'ADM Jabalpur arose during the National Emergency of 1975 when individuals were detained under preventive detention laws. The central question was whether detainees could approach courts through habeas corpus petitions when enforcement of certain Fundamental Rights had been suspended.\r\n\r\nThe majority of the Supreme Court held that such judicial remedies were unavailable during the Emergency in the circumstances before it. Justice H.R. Khanna dissented. The majority decision was later repudiated and overruled, becoming an important lesson on judicial independence and personal liberty.'),
(15, 16, 'Bachan Singh challenged the constitutional validity of the death penalty under Indian criminal law. The case examined whether capital punishment violated the right to life under Article 21 and whether its application could be constitutionally restricted.\r\n\r\nThe Supreme Court upheld the death penalty but established the rarest of rare doctrine. It held that capital punishment should be imposed only in exceptional cases where the alternative of life imprisonment is unquestionably inadequate. The judgment remains a central authority on sentencing in death penalty cases.'),
(16, 17, 'Mithu challenged Section 303 of the Indian Penal Code, which prescribed mandatory death punishment for a person serving a life sentence who committed murder. The provision removed judicial discretion and prevented courts from considering individual circumstances.\r\n\r\nThe Supreme Court struck down Section 303 as unconstitutional under Articles 14 and 21. It held that mandatory death punishment was arbitrary and deprived courts of the ability to examine relevant circumstances. The judgment strengthened the principle of fair and proportionate criminal sentencing.'),
(17, 18, 'Hussainara Khatoon highlighted the condition of undertrial prisoners in Bihar who had remained in custody for periods longer than the maximum punishment prescribed for their alleged offences. The petition brought attention to delays in criminal trials and the condition of prisoners unable to obtain timely justice.\r\n\r\nThe Supreme Court recognised the right to speedy trial as an essential part of Article 21. It directed the release of eligible undertrial prisoners and stressed that access to timely justice is a constitutional requirement. The case became a major development in prisoners’ rights and criminal justice reform.'),
(18, 19, 'Olga Tellis involved pavement and slum dwellers in Mumbai who faced eviction by municipal authorities. The petitioners argued that removal from their settlements would deprive them of their livelihood and affect their ability to survive in the city.\r\n\r\nThe Supreme Court recognised that the right to livelihood is connected with the right to life under Article 21. It held that eviction procedures must comply with the law and principles of natural justice. The judgment established an important constitutional connection between livelihood and human dignity.'),
(19, 20, 'The M.C. Mehta Oleum Gas Leak case arose after a leak of oleum gas from a chemical manufacturing unit in Delhi caused serious public harm. The incident raised questions about the responsibility of industries conducting hazardous activities and the legal principles governing industrial accidents.\r\n\r\nThe Supreme Court developed the principle of absolute liability for enterprises engaged in hazardous or inherently dangerous activities. Such enterprises are liable for harm caused by their activities without relying on traditional exceptions available under strict liability. The judgment became a major development in Indian environmental and industrial liability law.'),
(20, 21, 'Vellore Citizens Welfare Forum filed proceedings against industries in Tamil Nadu that were causing pollution through untreated industrial waste. The case highlighted the impact of industrial activity on agricultural land, water resources and public health.\r\n\r\nThe Supreme Court recognised the precautionary principle and polluter pays principle as important parts of Indian environmental law. It held that industries responsible for environmental damage could be required to bear the cost of restoration. The judgment strengthened the legal framework for sustainable environmental protection.'),
(21, 22, 'T.N. Godavarman Thirumulpad approached the Supreme Court concerning the protection and conservation of forest areas. The case examined the meaning of forests under the Forest Conservation Act, 1980 and the extent of governmental responsibility in preventing deforestation.\r\n\r\nThe Supreme Court adopted a broad interpretation of the term forest, extending protection beyond areas formally classified as forests in government records. It also initiated continuing judicial supervision of forest conservation. The proceedings became one of the most important examples of continuing mandamus in Indian environmental law.'),
(22, 23, 'Subhash Kumar filed a petition alleging that industrial operations were polluting the water of the Bokaro River and affecting the surrounding environment. The case raised the question of whether access to clean water and air could receive protection under the constitutional right to life.\r\n\r\nThe Supreme Court recognised that the right to life under Article 21 includes the right to enjoy pollution-free water and air. However, it dismissed the petition because it found that the proceedings were not brought for a genuine public interest purpose. The judgment remains relevant to environmental constitutional rights and misuse of PIL.'),
(23, 24, 'Lalita Kumari concerned the failure of police authorities to register an FIR despite receiving information about a cognisable offence. The petitioner sought judicial directions regarding the legal duty of police officers when information discloses a criminal offence.\r\n\r\nThe Supreme Court held that registration of an FIR is mandatory when information discloses a cognisable offence. It permitted limited preliminary inquiries in specified categories of cases to determine whether a cognisable offence is disclosed. The judgment clarified police responsibilities during the initial stage of criminal investigation.'),
(24, 25, 'Arnesh Kumar challenged the practice of unnecessary arrests in matrimonial disputes, particularly cases involving allegations under Section 498A of the Indian Penal Code. The petition highlighted how arrests were sometimes made without examining the necessity of taking a person into custody.\r\n\r\nThe Supreme Court issued directions requiring police officers to assess the necessity of arrest under Section 41 of the Code of Criminal Procedure. It also directed Magistrates to examine the reasons for arrest before authorising detention. The judgment strengthened safeguards against arbitrary arrests and unnecessary detention.'),
(25, 26, 'Lily Thomas challenged provisions that allowed convicted members of Parliament and State Legislatures to continue holding their seats during the pendency of appeals in certain circumstances. The case examined the constitutional and statutory framework governing disqualification of elected representatives.\r\n\r\nThe Supreme Court held that sitting legislators convicted and sentenced to imprisonment of at least two years would face immediate disqualification under the applicable law. It invalidated the protection previously available under Section 8(4) of the Representation of the People Act, 1951. The judgment strengthened the legal framework governing electoral integrity.'),
(26, 27, 'Sarla Mudgal concerned cases where Hindu husbands converted to Islam and entered into second marriages without legally dissolving their existing Hindu marriages. The proceedings examined the relationship between personal laws, religious conversion and the criminal law relating to bigamy.\r\n\r\nThe Supreme Court held that conversion to Islam alone does not automatically dissolve an existing Hindu marriage. A second marriage without dissolution of the first marriage would attract the law relating to bigamy. The judgment examined the interaction between personal laws and the protection of existing marital rights.'),
(27, 28, 'Shayara Bano challenged the practice of talaq-e-biddat, commonly known as instant triple talaq, through which a Muslim husband could pronounce divorce instantly. The petition raised questions about equality, dignity and the constitutional validity of the practice.\r\n\r\nThe Supreme Court, by a majority decision, invalidated instant triple talaq. The judges adopted different constitutional and legal approaches while reaching the result. The judgment was followed by legislative action through the Muslim Women (Protection of Rights on Marriage) Act, 2019.'),
(28, 29, 'Shafin Jahan concerned the marriage of Hadiya, an adult woman who had converted to Islam and married Shafin Jahan. The Kerala High Court had annulled the marriage, leading to an appeal before the Supreme Court regarding individual choice and personal liberty.\r\n\r\nThe Supreme Court restored the marriage and held that an adult individual has the freedom to choose a spouse. It recognised that personal decisions relating to marriage and faith fall within the sphere of individual autonomy. The judgment strengthened constitutional protection for freedom of choice and dignity.'),
(29, 30, 'The Aadhaar case involved challenges to the constitutional validity of the Aadhaar identification framework and its use by the government for welfare and other purposes. Petitioners raised concerns regarding privacy, personal information, surveillance and mandatory linking of identification with services.\r\n\r\nThe Supreme Court upheld the Aadhaar framework in substantial part while imposing limitations on its use. It restricted mandatory Aadhaar requirements in certain private transactions and struck down provisions that lacked adequate constitutional justification. The judgment addressed the balance between welfare administration and individual privacy.'),
(30, 31, 'Anuradha Bhasin challenged restrictions on internet access and communication services imposed in Jammu and Kashmir following the constitutional changes of August 2019. The proceedings raised questions about freedom of expression, trade and the legality of prolonged internet suspension.\r\n\r\nThe Supreme Court held that freedom of speech and expression and the freedom to carry on trade through the internet receive constitutional protection. It ruled that indefinite suspension of internet services is impermissible and directed authorities to periodically review restrictions. The judgment established important principles concerning internet shutdowns and civil liberties.'),
(31, 32, 'Common Cause filed proceedings seeking recognition of the right to die with dignity and the legal validity of advance medical directives. The case examined whether individuals could refuse life-sustaining medical treatment in circumstances involving terminal illness or irreversible medical conditions.\r\n\r\nThe Supreme Court recognised passive euthanasia and advance directives subject to prescribed safeguards. It held that the right to live with dignity under Article 21 can include the right to refuse certain medical treatment. The judgment established a constitutional framework for end-of-life medical decisions.'),
(32, 33, 'P.A. Inamdar concerned the rights of private unaided professional educational institutions regarding admissions and the imposition of reservation policies. The case examined the constitutional relationship between institutional autonomy, educational administration and state regulation.\r\n\r\nThe Supreme Court held that the state could not impose its reservation policy on private unaided professional institutions in the manner challenged. It recognised institutional autonomy while permitting reasonable regulation for maintaining educational standards. The judgment clarified the constitutional position of private professional educational institutions.'),
(33, 34, 'T.M.A. Pai Foundation examined the constitutional rights of religious and linguistic minorities and private educational institutions to establish and administer educational institutions. The case involved questions concerning admissions, administration, fees and governmental regulation.\r\n\r\nThe Supreme Court clarified the scope of Articles 19(1)(g), 29 and 30 in relation to educational institutions. It recognised the rights of minorities to establish and administer educational institutions while permitting reasonable regulatory measures. The judgment became a major authority on educational rights and institutional autonomy.'),
(34, 35, 'Unni Krishnan concerned the constitutional status of the right to education and the legality of admission and fee structures in private professional educational institutions. The proceedings examined whether access to education could be treated as part of the right to life under Article 21.\r\n\r\nThe Supreme Court recognised the right to education for children up to fourteen years as part of Article 21, subject to the state capacity framework considered by the Court. The judgment influenced the later introduction of Article 21A through the Eighty-sixth Constitutional Amendment. It also addressed the regulation of private educational institutions.'),
(35, 36, 'Mohini Jain challenged the practice of charging capitation fees for admission to professional educational institutions in Karnataka. The petitioner argued that excessive fees created unequal access to education and affected the constitutional rights of students.\r\n\r\nThe Supreme Court held that the right to education is connected with the right to life under Article 21. It found that education cannot be treated as a privilege available only to individuals who can afford high fees. The judgment contributed to the constitutional development of the right to education.'),
(36, 37, 'The M.C. Mehta Taj Trapezium proceedings concerned industrial pollution around the Taj Mahal and its impact on the historic monument. Emissions from nearby industries were considered a threat to the marble structure and the surrounding environment.\r\n\r\nThe Supreme Court directed measures to control industrial pollution in the Taj Trapezium Zone, including shifting certain industries away from polluting fuels. The judgment highlighted the responsibility of authorities and industries to protect cultural heritage and environmental quality. It became an important example of judicial intervention in environmental conservation.'),
(37, 38, 'Indian Council for Enviro-Legal Action concerned serious environmental damage caused by chemical industries in Rajasthan. Industrial waste had contaminated surrounding land and water resources, creating risks for nearby communities and agricultural activity.\r\n\r\nThe Supreme Court applied the polluter pays principle and held that industries responsible for environmental damage must bear the cost of restoration. It emphasised that economic activity cannot be carried out at the expense of public health and environmental protection. The judgment strengthened the enforcement of environmental liability in India.'),
(38, 39, 'Rural Litigation and Entitlement Kendra raised concerns about limestone quarrying in the Doon Valley and the resulting environmental degradation. The proceedings examined the impact of mining activities on forests, water resources, soil and the ecological balance of the region.\r\n\r\nThe Supreme Court directed the closure of environmentally harmful mining operations after considering the damage caused by quarrying. It recognised that environmental protection could justify restrictions on economic activity. The case became an early example of environmental public interest litigation in India.'),
(39, 40, 'Bandhua Mukti Morcha brought attention to the condition of workers employed in stone quarries in Haryana who were allegedly subjected to bonded labour and exploitative working conditions. The petition sought enforcement of constitutional protections and labour welfare legislation.\r\n\r\nThe Supreme Court adopted a broad approach to public interest litigation and recognised the importance of enforcing the rights of disadvantaged workers. It directed authorities to investigate conditions and take steps to protect bonded labourers. The judgment strengthened the connection between Article 21, human dignity and protection against forced labour.'),
(40, 41, 'PUCL filed proceedings concerning the interception of telephone conversations by government authorities. The case raised concerns about privacy, freedom of expression and the possibility of misuse of surveillance powers without adequate procedural safeguards.\r\n\r\nThe Supreme Court laid down procedural safeguards governing telephone interception under the Indian Telegraph Act. It required proper authorisation, review mechanisms and compliance with legal conditions. The judgment became an important authority on privacy protection and government surveillance.'),
(41, 42, 'People’s Union for Civil Liberties challenged the existing voting system on the ground that voters should have the ability to reject all candidates without revealing their political preference. The case raised questions about electoral secrecy and the meaning of freedom of expression in elections.\r\n\r\nThe Supreme Court directed the introduction of the NOTA option in electronic voting machines. It held that the right to vote and the freedom to express electoral preference include the ability to reject all contesting candidates while maintaining secrecy. The judgment strengthened voter choice and electoral participation.'),
(42, 43, 'L. Chandra Kumar concerned the constitutional validity and jurisdiction of administrative tribunals created to decide service and administrative disputes. The case examined whether tribunal decisions could exclude the jurisdiction of High Courts and the Supreme Court.\r\n\r\nThe Supreme Court held that judicial review under Articles 226, 227 and 32 is part of the Basic Structure of the Constitution. It ruled that tribunal decisions remain subject to scrutiny by the appropriate High Courts. The judgment established important limits on the power of tribunals and preserved constitutional judicial review.'),
(43, 44, 'Kihoto Hollohan concerned the constitutional validity of the Tenth Schedule, which introduced provisions against political defections by elected representatives. The petition challenged the role of the Speaker in deciding disqualification disputes and the restrictions on judicial review.\r\n\r\nThe Supreme Court upheld the constitutional validity of the anti-defection law. It held that decisions of the Speaker under the Tenth Schedule are subject to judicial review, although such review is generally available after the decision. The judgment clarified the relationship between legislative authority and constitutional judicial oversight.'),
(44, 45, 'Raja Ram Pal arose from the expulsion of Members of Parliament following allegations connected with the parliamentary cash-for-questions controversy. The case examined the extent of parliamentary privileges and whether courts could review decisions taken by Parliament.\r\n\r\nThe Supreme Court held that parliamentary privileges and expulsion decisions are not completely immune from judicial review. Courts may examine parliamentary action where there is constitutional illegality or substantive illegality. The judgment clarified the limits of parliamentary privilege under the Constitution.'),
(45, 46, 'R.C. Cooper, commonly known as the Bank Nationalisation case, challenged the government decision to nationalise fourteen major commercial banks. The petitioner questioned the impact of the legislation on property rights, business activities and other Fundamental Rights.\r\n\r\nThe Supreme Court examined the actual effect of state action on individual rights rather than treating constitutional rights as completely separate compartments. The judgment developed the interpretation of Fundamental Rights and influenced later constitutional decisions, including the approach adopted in Maneka Gandhi.'),
(46, 47, 'Bennett Coleman & Co. challenged restrictions imposed under the government newsprint policy, which affected the circulation and growth of newspapers. The petition argued that the restrictions interfered with the freedom of the press and the ability of newspapers to communicate information.\r\n\r\nThe Supreme Court held that the restrictions violated freedom of speech and expression under Article 19(1)(a). It recognised that freedom of the press includes the ability to circulate publications and reach readers. The judgment strengthened constitutional protection for independent journalism and media freedom.'),
(47, 48, 'Romesh Thappar concerned the prohibition imposed by the Madras government on the circulation of the journal Cross Roads. The petitioner challenged the restriction as a violation of freedom of speech and expression guaranteed under Article 19(1)(a).\r\n\r\nThe Supreme Court held that freedom of speech and expression includes the freedom to circulate publications. It invalidated the restriction under the constitutional provisions applicable at the time. The judgment became one of the earliest major decisions protecting press freedom in independent India.'),
(48, 49, 'State of Madras v. Champakam Dorairajan concerned a government order that distributed seats in educational institutions among different communities according to communal classifications. The respondent challenged the order on the ground that it violated constitutional guarantees of equality and non-discrimination.\r\n\r\nThe Supreme Court struck down the communal allocation order because it violated Fundamental Rights. The judgment led to the First Constitutional Amendment, which introduced Article 15(4) to permit special provisions for socially and educationally backward classes and certain other groups. The case became an important development in the constitutional history of reservation.'),
(49, 50, 'A.K. Gopalan challenged his preventive detention under the Preventive Detention Act, 1950, arguing that the detention violated his Fundamental Rights. The case examined the meaning of personal liberty and the relationship between Articles 14, 19, 21 and 22.\r\n\r\nThe Supreme Court adopted a narrow interpretation of personal liberty and treated several Fundamental Rights as separate constitutional guarantees. This approach was substantially departed from in later decisions, particularly Maneka Gandhi. The judgment remains historically important in understanding the development of Article 21 and constitutional liberty.');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `authkey` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `password`, `authkey`) VALUES
(3, 'aloo', '$2y$10$xgUS6G/g99XnzltaHD/NnuFV7VS7seQml2ZHsgi8xJUPS7OyVXwnm', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD PRIMARY KEY (`bookmark_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `legal_document_id` (`legal_document_id`);

--
-- Indexes for table `cases`
--
ALTER TABLE `cases`
  ADD PRIMARY KEY (`cid`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `case_info`
--
ALTER TABLE `case_info`
  ADD PRIMARY KEY (`i_id`),
  ADD KEY `case_id` (`case_id`);

--
-- Indexes for table `case_tasks`
--
ALTER TABLE `case_tasks`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `case_id` (`case_id`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`client_id`),
  ADD KEY `idx_practitioner_id` (`practitioner_id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`d_id`),
  ADD KEY `case_id` (`case_id`);

--
-- Indexes for table `hearing`
--
ALTER TABLE `hearing`
  ADD PRIMARY KEY (`h_id`),
  ADD KEY `d_id` (`d_id`),
  ADD KEY `c_id` (`c_id`);

--
-- Indexes for table `legal_documents`
--
ALTER TABLE `legal_documents`
  ADD PRIMARY KEY (`legal_document_id`),
  ADD UNIQUE KEY `d_id` (`d_id`);

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `case_id` (`case_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookmarks`
--
ALTER TABLE `bookmarks`
  MODIFY `bookmark_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cases`
--
ALTER TABLE `cases`
  MODIFY `cid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `case_info`
--
ALTER TABLE `case_info`
  MODIFY `i_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `case_tasks`
--
ALTER TABLE `case_tasks`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `client`
--
ALTER TABLE `client`
  MODIFY `client_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `d_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `hearing`
--
ALTER TABLE `hearing`
  MODIFY `h_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `legal_documents`
--
ALTER TABLE `legal_documents`
  MODIFY `legal_document_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD CONSTRAINT `bookmarks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookmarks_ibfk_2` FOREIGN KEY (`legal_document_id`) REFERENCES `legal_documents` (`legal_document_id`) ON DELETE CASCADE;

--
-- Constraints for table `cases`
--
ALTER TABLE `cases`
  ADD CONSTRAINT `cases_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `client` (`client_id`),
  ADD CONSTRAINT `cases_ibfk_2` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `case_info`
--
ALTER TABLE `case_info`
  ADD CONSTRAINT `case_info_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`cid`) ON DELETE CASCADE;

--
-- Constraints for table `case_tasks`
--
ALTER TABLE `case_tasks`
  ADD CONSTRAINT `case_tasks_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`cid`) ON DELETE CASCADE,
  ADD CONSTRAINT `case_tasks_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`);

--
-- Constraints for table `client`
--
ALTER TABLE `client`
  ADD CONSTRAINT `fk_client_practitioner` FOREIGN KEY (`practitioner_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`cid`) ON DELETE CASCADE;

--
-- Constraints for table `hearing`
--
ALTER TABLE `hearing`
  ADD CONSTRAINT `hearing_ibfk_1` FOREIGN KEY (`d_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `hearing_ibfk_2` FOREIGN KEY (`c_id`) REFERENCES `cases` (`cid`) ON DELETE CASCADE;

--
-- Constraints for table `legal_documents`
--
ALTER TABLE `legal_documents`
  ADD CONSTRAINT `fk_legal_document_file` FOREIGN KEY (`d_id`) REFERENCES `documents` (`d_id`) ON DELETE CASCADE;

--
-- Constraints for table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`cid`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
