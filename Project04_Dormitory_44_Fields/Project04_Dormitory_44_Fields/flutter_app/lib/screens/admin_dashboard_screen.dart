import 'package:flutter/material.dart';
import '../services/api_service.dart';
import '../models/room.dart';
import '../models/room_type.dart';

class AdminDashboardScreen extends StatefulWidget {
  const AdminDashboardScreen({super.key});

  @override
  State<AdminDashboardScreen> createState() => _AdminDashboardScreenState();
}

class _AdminDashboardScreenState extends State<AdminDashboardScreen> {
  Map<String, dynamic> _stats = {};
  List<Room> _rooms = [];
  List<RoomType> _roomTypes = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    setState(() => _isLoading = true);
    final stats = await ApiService.getStats();
    final rooms = await ApiService.getRooms();
    final types = await ApiService.getRoomTypes();

    setState(() {
      _stats = stats;
      _rooms = rooms;
      _roomTypes = types;
      _isLoading = false;
    });
  }

  void _showAddRoomDialog() {
    final numCtrl = TextEditingController();
    final descCtrl = TextEditingController();
    int floor = 1;
    int? typeId = _roomTypes.isNotEmpty ? _roomTypes.first.id : null;
    String status = 'available';

    showDialog(
      context: context,
      builder: (ctx) => StatefulBuilder(
        builder: (context, setDState) => AlertDialog(
          title: const Text('เพิ่มห้องพักใหม่', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                TextField(controller: numCtrl, decoration: const InputDecoration(labelText: 'หมายเลขห้อง (เช่น 105)')),
                const SizedBox(height: 8),
                DropdownButtonFormField<int>(
                  value: typeId,
                  decoration: const InputDecoration(labelText: 'ประเภทห้อง'),
                  items: _roomTypes.map((t) => DropdownMenuItem(value: t.id, child: Text(t.name))).toList(),
                  onChanged: (v) => setDState(() => typeId = v),
                ),
                const SizedBox(height: 8),
                DropdownButtonFormField<String>(
                  value: status,
                  decoration: const InputDecoration(labelText: 'สถานะเริ่มต้น'),
                  items: const [
                    DropdownMenuItem(value: 'available', child: Text('ว่าง (Available)')),
                    DropdownMenuItem(value: 'occupied', child: Text('มีผู้พัก (Occupied)')),
                    DropdownMenuItem(value: 'maintenance', child: Text('ปิดปรับปรุง (Maintenance)')),
                  ],
                  onChanged: (v) => setDState(() => status = v ?? 'available'),
                ),
                const SizedBox(height: 8),
                TextField(controller: descCtrl, decoration: const InputDecoration(labelText: 'รายละเอียด')),
              ],
            ),
          ),
          actions: [
            TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('ยกเลิก')),
            ElevatedButton(
              onPressed: () async {
                if (numCtrl.text.trim().isEmpty || typeId == null) return;
                await ApiService.addRoom(numCtrl.text.trim(), typeId!, floor, status, descCtrl.text.trim());
                Navigator.pop(ctx);
                _loadData();
              },
              child: const Text('บันทึก'),
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('แผงควบคุมผู้ดูแลหอพัก'),
        backgroundColor: Colors.indigo,
        foregroundColor: Colors.white,
        actions: [
          IconButton(icon: const Icon(Icons.refresh), onPressed: _loadData),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: _showAddRoomDialog,
        backgroundColor: Colors.indigo,
        foregroundColor: Colors.white,
        icon: const Icon(Icons.add_home),
        label: const Text('เพิ่มห้องพัก'),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : ListView(
              padding: const EdgeInsets.all(16.0),
              children: [
                // Stats
                Row(
                  children: [
                    _buildStatCard('ห้องทั้งหมด', _stats['total_rooms']?.toString() ?? '0', Colors.indigo),
                    const SizedBox(width: 8),
                    _buildStatCard('ว่างพร้อมจอง', _stats['available_rooms']?.toString() ?? '0', Colors.green),
                    const SizedBox(width: 8),
                    _buildStatCard('มีผู้เช่า', _stats['occupied_rooms']?.toString() ?? '0', Colors.red),
                  ],
                ),
                const SizedBox(height: 20),
                const Text('รายการห้องพักทั้งหมด', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                const SizedBox(height: 8),
                ..._rooms.map((r) => Card(
                      margin: const EdgeInsets.only(bottom: 8),
                      child: ListTile(
                        leading: CircleAvatar(
                          backgroundColor: r.isAvailable ? Colors.green.shade100 : Colors.red.shade100,
                          child: Icon(Icons.meeting_room, color: r.isAvailable ? Colors.green : Colors.red),
                        ),
                        title: Text("ห้อง " + r.roomNumber + " (ชั้น " + r.floor.toString() + ")", style: const TextStyle(fontWeight: FontWeight.bold)),
                        subtitle: Text((r.roomType?.name ?? '-') + " • ฿" + (r.roomType?.monthlyRate.toStringAsFixed(2) ?? '0.00')),
                        trailing: Chip(
                          label: Text(r.status),
                          backgroundColor: r.isAvailable ? Colors.green.shade50 : Colors.amber.shade50,
                          labelStyle: TextStyle(fontSize: 10, color: r.isAvailable ? Colors.green : Colors.orange),
                        ),
                      ),
                    )),
              ],
            ),
    );
  }

  Widget _buildStatCard(String label, String value, Color color) {
    return Expanded(
      child: Card(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        child: Padding(
          padding: const EdgeInsets.all(12.0),
          child: Column(
            children: [
              Text(label, style: const TextStyle(fontSize: 11, color: Colors.grey)),
              const SizedBox(height: 4),
              Text(value, style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: color)),
            ],
          ),
        ),
      ),
    );
  }
}
