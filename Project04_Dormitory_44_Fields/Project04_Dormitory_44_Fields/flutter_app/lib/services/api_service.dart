import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../models/user.dart';
import '../models/room.dart';
import '../models/room_type.dart';
import '../models/booking.dart';

class ApiService {
  static const String baseUrl = 'http://localhost:8080/api';
  static String? token;
  static User? currentUser;

  static Future<void> init() async {
    final prefs = await SharedPreferences.getInstance();
    token = prefs.getString('auth_token');
    if (token != null) {
      await fetchCurrentUser();
    }
  }

  static Map<String, String> get headers {
    final Map<String, String> h = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };
    if (token != null && token!.isNotEmpty) {
      h['Authorization'] = 'Bearer ' + token!;
    }
    return h;
  }

  static Future<bool> login(String email, String password) async {
    try {
      final res = await http.post(
        Uri.parse("$baseUrl/login"),
        headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
        body: jsonEncode({'email': email, 'password': password}),
      );
      final data = jsonDecode(res.body);
      if (res.statusCode == 200 && data['success'] == true) {
        token = data['token'];
        currentUser = User.fromJson(data['user']);
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('auth_token', token!);
        return true;
      }
    } catch (_) {}
    return false;
  }

  static Future<bool> register(String name, String email, String phone, String password) async {
    try {
      final res = await http.post(
        Uri.parse("$baseUrl/register"),
        headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
        body: jsonEncode({
          'name': name,
          'email': email,
          'phone': phone,
          'password': password,
        }),
      );
      final data = jsonDecode(res.body);
      if (res.statusCode == 201 && data['success'] == true) {
        token = data['token'];
        currentUser = User.fromJson(data['user']);
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('auth_token', token!);
        return true;
      }
    } catch (_) {}
    return false;
  }

  static Future<void> logout() async {
    if (token != null) {
      try {
        await http.post(Uri.parse("$baseUrl/logout"), headers: headers);
      } catch (_) {}
    }
    token = null;
    currentUser = null;
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');
  }

  static Future<void> fetchCurrentUser() async {
    try {
      final res = await http.get(Uri.parse("$baseUrl/me"), headers: headers);
      if (res.statusCode == 200) {
        final data = jsonDecode(res.body);
        if (data['success'] == true) {
          currentUser = User.fromJson(data['user']);
        }
      }
    } catch (_) {}
  }

  static Future<List<RoomType>> getRoomTypes() async {
    try {
      final res = await http.get(Uri.parse("$baseUrl/room-types"), headers: headers);
      if (res.statusCode == 200) {
        final data = jsonDecode(res.body);
        if (data['success'] == true) {
          return (data['data'] as List).map((e) => RoomType.fromJson(e)).toList();
        }
      }
    } catch (_) {}
    return [];
  }

  static Future<List<Room>> getRooms({String? status, int? roomTypeId, int? floor}) async {
    try {
      String query = '';
      if (status != null) query += "status=" + status + "&";
      if (roomTypeId != null) query += "room_type_id=" + roomTypeId.toString() + "&";
      if (floor != null) query += "floor=" + floor.toString() + "&";

      final res = await http.get(Uri.parse("$baseUrl/rooms?" + query), headers: headers);
      if (res.statusCode == 200) {
        final data = jsonDecode(res.body);
        if (data['success'] == true) {
          return (data['data'] as List).map((e) => Room.fromJson(e)).toList();
        }
      }
    } catch (_) {}
    return [];
  }

  static Future<Booking?> getBookingByCode(String code) async {
    try {
      final res = await http.get(Uri.parse("$baseUrl/bookings/" + code), headers: headers);
      if (res.statusCode == 200) {
        final data = jsonDecode(res.body);
        if (data['success'] == true) {
          return Booking.fromJson(data['data']);
        }
      }
    } catch (_) {}
    return null;
  }

  static Future<List<Booking>> getMyBookings() async {
    try {
      final res = await http.get(Uri.parse("$baseUrl/my-bookings"), headers: headers);
      if (res.statusCode == 200) {
        final data = jsonDecode(res.body);
        if (data['success'] == true) {
          return (data['data'] as List).map((e) => Booking.fromJson(e)).toList();
        }
      }
    } catch (_) {}
    return [];
  }

  static Future<Map<String, dynamic>> createBooking(int roomId, String checkInDate) async {
    try {
      final res = await http.post(
        Uri.parse("$baseUrl/bookings"),
        headers: headers,
        body: jsonEncode({
          'room_id': roomId,
          'check_in_date': checkInDate,
        }),
      );
      return jsonDecode(res.body);
    } catch (_) {
      return {'success': false, 'message': 'เกิดข้อผิดพลาดในการเชื่อมต่อ'};
    }
  }

  static Future<Map<String, dynamic>> updateBookingStatus(int id, String status) async {
    try {
      final res = await http.post(
        Uri.parse("$baseUrl/bookings/" + id.toString() + "/status"),
        headers: headers,
        body: jsonEncode({'status': status}),
      );
      return jsonDecode(res.body);
    } catch (_) {
      return {'success': false, 'message': 'เกิดข้อผิดพลาด'};
    }
  }

  static Future<Map<String, dynamic>> addRoom(String roomNumber, int roomTypeId, int floor, String status, String description) async {
    try {
      final res = await http.post(
        Uri.parse("$baseUrl/rooms"),
        headers: headers,
        body: jsonEncode({
          'room_number': roomNumber,
          'room_type_id': roomTypeId,
          'floor': floor,
          'status': status,
          'description': description,
        }),
      );
      return jsonDecode(res.body);
    } catch (_) {
      return {'success': false, 'message': 'เกิดข้อผิดพลาด'};
    }
  }

  static Future<Map<String, dynamic>> getStats() async {
    try {
      final res = await http.get(Uri.parse("$baseUrl/stats"), headers: headers);
      if (res.statusCode == 200) {
        final data = jsonDecode(res.body);
        if (data['success'] == true) {
          return data['data'];
        }
      }
    } catch (_) {}
    return {};
  }
}
