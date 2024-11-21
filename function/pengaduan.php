<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//insert data report

function buat_pengaduan($user_id, $message, $image, $conn)
{
    //prepare SQL Injectioin -> mencegah pihak luar memanipulasi data yang ada pada database
    $stmt = $conn->prepare("INSERT INTO reports (user_id, message, image, status, created_at) VALUES (?, ?, ?, ?, NOW())");

    //memeberrikan default value
    $status = 'proses';

    //menghubungkan argumen dengan query
    $stmt->bind_param("ssss", $user_id, $message, $image, $status);

    //eksekusi, menyimpan 
    return $stmt->execute();
}

// mendapatkan data report sesuai id_user

function get_pengaduan_by_status($username, $status, $conn)
{
    //mendapatkan id_user
    $query_id = "SELECT id FROM users WHERE username = '$username'";
    $result_id = mysqli_query($conn, $query_id);
    $row = mysqli_fetch_assoc($result_id);
    $id = $row['id'];

    //mengambil data laporan sesuai status 
    $query = "SELECT * FROM reports WHERE user_id = '$id' AND status = '$status'";
    $result = mysqli_query($conn, $query);

    //menyimpan data yang sudah dipatkan
    $pengaduan = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $pengaduan[] = $row;
    }

    return $pengaduan;
}

//mendapatkan data sesuai status
function get_all_pengaduan_by_status($status, $conn)
{
    // membuat query/permintaan

    $query = "SELECT * FROM reports WHERE status = ?";

    //prepared stetment
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $status);

    //eksekusi
    $stmt->execute();

    //mengambil hasil query
    $result = $stmt->get_result();

    $pengaduan = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $pengaduan[] = $row;
    }

    return $pengaduan;
}

////menambahkan feedback
function addFeedback($report_id, $feedback, $petugas_id, $conn)
{
    //kita melakukan 2 operasi sekaligus
    //1. menambahkan feedback
    //2. mengubah status report yang taduya proses menjadi selesai

    $conn->begin_transaction();

    //menyiapkan query

    try {
        $stmt = $conn->prepare("INSERT INTO feedbacks(report_id, feedback, petugas_id, created_at) VALUES (?,?,?,NOW())");
        $stmt->bind_param("isi", $report_id, $feedback, $petugas_id);

        //eksekusi query
        if (!$stmt->execute()) {
            throw new Exception("gagal menyimpan feedback");
        }

        //mengubah status report
        $updateStmt = $conn->prepare("UPDATE reports SET status = 'selesai' WHERE id = ?");
        $updateStmt->bind_param("i", $report_id);

        //eksekusi query
        if (!$updateStmt->execute()) {
            throw new Exception("gagal mupdate status report");
        }

        //jika semua operasi berhasil
        $conn->commit(); //menyimpan di database
        return true; //operasi yang kita laukan berhasil


    } catch (\Throwable $error) {
        //ini kalo operasinya ggl
        $conn->rollback(); //membatalkan semua perubahan
        echo "Error: " . $error->getMessage();
        return false;
    }
}

// get reports with feedback by status selesai

function get_reports_with_feedbacks_by_status($conn)
{
    $query = "SELECT
                    reports.id,
                    users.username AS pelapor, 
                    reports.message,
                    reports.image,
                    reports.created_at AS report_date, 
                    feedbacks.feedback,
                    feedbacks.created_at AS feedback_date
                FROM reports 
                LEFT JOIN feedbacks ON reports.id = feedbacks.report_id
                LEFT JOIN users ON reports.user_id = users.id
                WHERE reports.status = 'selesai'
                ORDER BY reports.created_at DESC";

    $result = $conn->query($query);

    // menyimpan data hasil query
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

// get reports with feedback by user and by status

function get_reports_with_feedback_by_user($username, $conn)
{
    //ambil user id berdasarkan yang login
    $user_id = $conn->query("SELECT id FROM users WHERE username = '$username'")->fetch_assoc()['id'];

    //mendapatkan laporan dengan feedback dengan filter user yang sedang login
    $query = "SELECT
                reports.id,
                reports.message,
                reports.created_at AS report_date,
                feedbacks.feedback,
                feedbacks.created_at AS feedback_date
                FROM reports
                LEFT JOIN feedbacks ON reports.id = feedbacks.report_id
                WHERE reports.user_id = ? AND reports.status = 'selesai'
                ORDER BY reports.created_at DESC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}
