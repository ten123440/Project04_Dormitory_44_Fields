import 'package:flutter/material.dart';
import '../services/api_service.dart';
import '../models/room.dart';
import '../models/room_type.dart';
import '../models/booking.dart';
import 'login_screen.dart';
import 'create_booking_screen.dart';
import 'booking_detail_screen.dart';
import 'admin_dashboard_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  int _selectedIndex = 0;
  List<Room> _rooms = [];
  List<RoomType> _roomTypes = [];
  List<Booking> _myBookings = [];
  bool _isLoading = true;
  final _trackController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _loadAll();
  }

  Future<void> _loadAll() async {
    setState(() => _isLoading = true);
    final rooms = await ApiService.getRooms();
    final types = await ApiService.getRoomTypes();
    List<Booking> myB = [];
    if (ApiService.token != null) {
      myB = await ApiService.getMyBookings();
    }
    setState(() {
      _rooms = rooms;
      _roomTypes = types;
      _myBookings = myB;
      _isLoading = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Dormitory Hub (Flutter Client)', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
        backgroundColor: Colors.indigo,
        foregroundColor: Colors.white,
        actions: [
          if (ApiService.currentUser == null)
            TextButton.icon(
              onPressed: () async {
                await Navigator.push(context, MaterialPageRoute(builder: (_) => const LoginScreen()));
                _loadAll();
              },
              icon: const Icon(Icons.login, color: Colors.white, size: 18),
              label: const Text('เข้าสู่ระบบ', style: TextStyle(color: Colors.white, fontSize: 12)),
            )
          else ...[
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 8.0),
              child: Center(
                child: Text(
                  ApiService.currentUser!.name,
                  style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold),
                ),
              ),
            ),
            IconButton(
              icon: const Icon(Icons.logout, size: 20),
              onPressed: () async {
                await ApiService.logout();
                _loadAll();
              },
            ),
          ],
        ],
      ),
      bottomNavigationBar: NavigationBar(
        selectedIndex: _selectedIndex,
        onDestinationSelected: (idx) {
          if (idx == 3) {
            if (ApiService.currentUser == null || !ApiService.currentUser!.isAdmin) {
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(content: Text('สำหรับผู้ดูแลหอพัก (Admin) เท่านั้น')),
              );
              return;
            }
            Navigator.push(context, MaterialPageRoute(builder: (_) => const AdminDashboardScreen()));
            return;
          }
          setState(() => _selectedIndex = idx);
          if (idx == 2) _loadAll();
        },
        destinations: const [
          NavigationDestination(icon: Icon(Icons.meeting_room), label: 'ห้องพัก'),
          NavigationDestination(icon: Icon(Icons.search), label: 'ค้นหาสัญญา'),
          NavigationDestination(icon: Icon(Icons.receipt_long), label: 'สัญญาของฉัน'),
          NavigationDestination(icon: Icon(Icons.admin_panel_settings), label: 'ผู้ดูแล'),
        ],
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _selectedIndex == 0
              ? _buildRoomsTab()
              : _selectedIndex == 1
                  ? _buildTrackTab()
                  : _buildMyBookingsTab(),
    );
  }

  Widget _buildRoomsTab() {
    return RefreshIndicator(
      onRefresh: _loadAll,
      child: ListView(
        padding: const EdgeInsets.all(16.0),
        children: [
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              gradient: const LinearGradient(colors: [Colors.indigo, Colors.blue]),
              borderRadius: BorderRadius.circular(16),
            ),
            child: const Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('ระบบจองห้องพักนักศึกษา', style: TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)),
                SizedBox(height: 4),
                Text('เลือกดูห้องพักว่างและทำสัญญาจองออนไลน์ได้ทันที', style: TextStyle(color: Colors.white70, fontSize: 12)),
              ],
            ),
          ),
          const SizedBox(height: 16),
          const Text('รายการห้องพักทั้งหมด', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
          const SizedBox(height: 8),
          ..._rooms.map((r) => Card(
                margin: const EdgeInsets.only(bottom: 12),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                child: Padding(
                  padding: const EdgeInsets.all(14.0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text("ห้อง " + r.roomNumber, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                          Chip(
                            label: Text(r.status),
                            backgroundColor: r.isAvailable ? Colors.green.shade100 : Colors.red.shade100,
                            labelStyle: TextStyle(
                              color: r.isAvailable ? Colors.green.shade900 : Colors.red.shade900,
                              fontWeight: FontWeight.bold,
                              fontSize: 11,
                            ),
                          ),
                        ],
                      ),
                      Text("ประเภท: " + (r.roomType?.name ?? '-')),
                      Text("ชั้น: " + r.floor.toString()),
                      Text("ค่าเช่า: ฿" + (r.roomType?.monthlyRate.toStringAsFixed(2) ?? '0.00') + " / เดือน",
                          style: const TextStyle(color: Colors.indigo, fontWeight: FontWeight.bold)),
                      if (r.description != null && r.description!.isNotEmpty) ...[
                        const SizedBox(height: 4),
                        Text(r.description!, style: const TextStyle(fontSize: 11, color: Colors.grey)),
                      ],
                      const SizedBox(height: 10),
                      SizedBox(
                        width: double.infinity,
                        child: ElevatedButton.icon(
                          onPressed: r.isAvailable
                              ? () async {
                                  if (ApiService.currentUser == null) {
                                    await Navigator.push(context, MaterialPageRoute(builder: (_) => const LoginScreen()));
                                  }
                                  if (ApiService.currentUser != null) {
                                    await Navigator.push(context, MaterialPageRoute(builder: (_) => CreateBookingScreen(room: r)));
                                    _loadAll();
                                  }
                                }
                              : null,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.indigo,
                            foregroundColor: Colors.white,
                          ),
                          icon: const Icon(Icons.bookmark_add, size: 16),
                          label: Text(r.isAvailable ? 'จองห้องพักนี้' : 'ไม่สามารถจองได้'),
                        ),
                      ),
                    ],
                  ),
                ),
              )),
        ],
      ),
    );
  }

  Widget _buildTrackTab() {
    return Padding(
      padding: const EdgeInsets.all(16.0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('ค้นหาและติดตามสัญญาเช่า', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
          const SizedBox(height: 6),
          const Text('กรอกรหัสสัญญา เช่น DORM-2026001', style: TextStyle(color: Colors.grey, fontSize: 12)),
          const SizedBox(height: 12),
          Row(
            children: [
              Expanded(
                child: TextField(
                  controller: _trackController,
                  decoration: const InputDecoration(hintText: 'DORM-2026001', border: OutlineInputBorder()),
                ),
              ),
              const SizedBox(width: 8),
              ElevatedButton(
                onPressed: () {
                  final code = _trackController.text.trim();
                  if (code.isNotEmpty) {
                    Navigator.push(context, MaterialPageRoute(builder: (_) => BookingDetailScreen(trackingCode: code)));
                  }
                },
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.indigo,
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
                ),
                child: const Text('ค้นหา'),
              ),
            ],
          ),
          const SizedBox(height: 16),
          Wrap(
            spacing: 8,
            children: [
              ActionChip(
                label: const Text('DORM-2026001'),
                onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const BookingDetailScreen(trackingCode: 'DORM-2026001'))),
              ),
              ActionChip(
                label: const Text('DORM-2026002'),
                onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const BookingDetailScreen(trackingCode: 'DORM-2026002'))),
              ),
              ActionChip(
                label: const Text('DORM-2026003'),
                onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const BookingDetailScreen(trackingCode: 'DORM-2026003'))),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildMyBookingsTab() {
    if (ApiService.currentUser == null) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(Icons.lock_outline, size: 48, color: Colors.grey),
            const SizedBox(height: 12),
            const Text('กรุณาเข้าสู่ระบบเพื่อดูสัญญาของคุณ'),
            const SizedBox(height: 12),
            ElevatedButton(
              onPressed: () async {
                await Navigator.push(context, MaterialPageRoute(builder: (_) => const LoginScreen()));
                _loadAll();
              },
              child: const Text('เข้าสู่ระบบ'),
            ),
          ],
        ),
      );
    }

    return RefreshIndicator(
      onRefresh: _loadAll,
      child: _myBookings.isEmpty
          ? const Center(child: Text('คุณยังไม่มีประวัติสัญญาเช่า'))
          : ListView.builder(
              padding: const EdgeInsets.all(16),
              itemCount: _myBookings.length,
              itemBuilder: (ctx, i) {
                final b = _myBookings[i];
                return Card(
                  margin: const EdgeInsets.only(bottom: 12),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  child: ListTile(
                    title: Text("สัญญา: " + b.trackingCode, style: const TextStyle(fontWeight: FontWeight.bold)),
                    subtitle: Text("ห้อง: " + (b.room?.roomNumber ?? '-') + " • เริ่ม: " + b.checkInDate),
                    trailing: Chip(
                      label: Text(b.status),
                      backgroundColor: b.isActive ? Colors.green.shade50 : Colors.amber.shade50,
                      labelStyle: TextStyle(fontSize: 10, color: b.isActive ? Colors.green : Colors.orange),
                    ),
                    onTap: () {
                      Navigator.push(ctx, MaterialPageRoute(builder: (_) => BookingDetailScreen(trackingCode: b.trackingCode)));
                    },
                  ),
                );
              },
            ),
    );
  }
}
