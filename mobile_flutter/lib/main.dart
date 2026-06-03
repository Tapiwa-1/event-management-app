import 'dart:async';
import 'dart:convert';
import 'dart:io';

import 'package:flutter/material.dart';

const apiBaseUrl = String.fromEnvironment(
  'API_BASE_URL',
  defaultValue: 'http://10.0.2.2:8000/api/v1',
);

void main() {
  runApp(const EventManagementApp());
}

class EventManagementApp extends StatelessWidget {
  const EventManagementApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Event Management',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(
          seedColor: const Color(0xff111827),
          primary: const Color(0xff111827),
          surface: const Color(0xffffffff),
        ),
        scaffoldBackgroundColor: const Color(0xfff8fafc),
        fontFamily: 'Roboto',
        useMaterial3: true,
      ),
      home: const AppShell(),
    );
  }
}

class ApiClient {
  const ApiClient(this.baseUrl);

  final String baseUrl;

  Future<Map<String, dynamic>> getMap(String path) async {
    final response = await _request('GET', path);
    return jsonDecode(response) as Map<String, dynamic>;
  }

  Future<List<dynamic>> getList(String path) async {
    final response = await _request('GET', path);
    return jsonDecode(response) as List<dynamic>;
  }

  Future<Map<String, dynamic>> post(String path, Map<String, dynamic> data) async {
    final response = await _request('POST', path, data: data);
    return jsonDecode(response) as Map<String, dynamic>;
  }

  Future<String> _request(String method, String path, {Map<String, dynamic>? data}) async {
    final uri = Uri.parse('$baseUrl$path');
    final client = HttpClient();
    try {
      final request = await client.openUrl(method, uri).timeout(const Duration(seconds: 12));
      request.headers.set(HttpHeaders.contentTypeHeader, 'application/json');
      request.headers.set(HttpHeaders.acceptHeader, 'application/json');
      if (data != null) {
        request.write(jsonEncode(data));
      }
      final response = await request.close().timeout(const Duration(seconds: 20));
      final body = await response.transform(utf8.decoder).join();
      if (response.statusCode < 200 || response.statusCode >= 300) {
        throw ApiException('HTTP ${response.statusCode}: $body');
      }
      return body;
    } finally {
      client.close(force: true);
    }
  }
}

class ApiException implements Exception {
  ApiException(this.message);
  final String message;
  @override
  String toString() => message;
}

class AppShell extends StatefulWidget {
  const AppShell({super.key});

  @override
  State<AppShell> createState() => _AppShellState();
}

class _AppShellState extends State<AppShell> {
  final ApiClient api = const ApiClient(apiBaseUrl);
  late Future<Map<String, dynamic>> data;
  int tab = 0;

  final tabs = const [
    ('Dashboard', Icons.dashboard_outlined),
    ('Clients', Icons.people_alt_outlined),
    ('Events', Icons.calendar_month_outlined),
    ('Quotes', Icons.assignment_outlined),
    ('Money', Icons.payments_outlined),
    ('Resources', Icons.inventory_2_outlined),
    ('Reports', Icons.bar_chart_outlined),
  ];

  @override
  void initState() {
    super.initState();
    data = api.getMap('/mobile/bootstrap');
  }

  Future<void> refresh() async {
    setState(() {
      data = api.getMap('/mobile/bootstrap');
    });
    await data;
  }

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<Map<String, dynamic>>(
      future: data,
      builder: (context, snapshot) {
        final isWide = MediaQuery.sizeOf(context).width >= 900;
        final content = _content(snapshot);

        return Scaffold(
          appBar: AppBar(
            title: Text(tabs[tab].$1),
            surfaceTintColor: Colors.transparent,
            actions: [
              IconButton(
                tooltip: 'Refresh',
                onPressed: refresh,
                icon: const Icon(Icons.refresh),
              ),
            ],
          ),
          body: isWide
              ? Row(
                  children: [
                    NavigationRail(
                      selectedIndex: tab,
                      onDestinationSelected: (index) => setState(() => tab = index),
                      labelType: NavigationRailLabelType.all,
                      destinations: [
                        for (final item in tabs)
                          NavigationRailDestination(
                            icon: Icon(item.$2),
                            selectedIcon: Icon(item.$2, fill: 1),
                            label: Text(item.$1),
                          ),
                      ],
                    ),
                    const VerticalDivider(width: 1),
                    Expanded(child: content),
                  ],
                )
              : content,
          bottomNavigationBar: isWide
              ? null
              : NavigationBar(
                  selectedIndex: tab,
                  onDestinationSelected: (index) => setState(() => tab = index),
                  destinations: [
                    for (final item in tabs)
                      NavigationDestination(icon: Icon(item.$2), label: item.$1),
                  ],
                ),
        );
      },
    );
  }

  Widget _content(AsyncSnapshot<Map<String, dynamic>> snapshot) {
    if (snapshot.connectionState == ConnectionState.waiting) {
      return const Center(child: CircularProgressIndicator());
    }

    if (snapshot.hasError) {
      return ErrorState(error: snapshot.error.toString(), onRetry: refresh);
    }

    final payload = snapshot.data ?? {};

    return RefreshIndicator(
      onRefresh: refresh,
      child: SingleChildScrollView(
        physics: const AlwaysScrollableScrollPhysics(),
        padding: const EdgeInsets.all(16),
        child: switch (tab) {
          0 => DashboardPage(payload: payload),
          1 => ClientsPage(payload: payload, api: api, onSaved: refresh),
          2 => EventsPage(payload: payload, api: api, onSaved: refresh),
          3 => QuotationsPage(payload: payload, api: api, onSaved: refresh),
          4 => MoneyPage(payload: payload, api: api, onSaved: refresh),
          5 => ResourcesPage(payload: payload, api: api, onSaved: refresh),
          _ => ReportsPage(payload: payload),
        },
      ),
    );
  }
}

class DashboardPage extends StatelessWidget {
  const DashboardPage({super.key, required this.payload});

  final Map<String, dynamic> payload;

  @override
  Widget build(BuildContext context) {
    final dashboard = map(payload['dashboard']);
    final stats = map(dashboard['stats']);
    final upcoming = list(dashboard['upcoming_events']);
    final popular = list(dashboard['popular_services']);

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const PageTitle('Event Management', 'Revenue, bookings, resources, and profitability.'),
        ResponsiveGrid(
          children: [
            StatCard('Upcoming events', '${stats['upcoming_events'] ?? 0}'),
            StatCard('Monthly revenue', money(stats['monthly_revenue'])),
            StatCard('Monthly expenses', money(stats['monthly_expenses'])),
            StatCard('Monthly profit', money(stats['monthly_profit'])),
            StatCard('Outstanding', money(stats['outstanding_payments'])),
          ],
        ),
        SectionCard(
          title: 'Upcoming Events',
          child: DataList(
            empty: 'No upcoming events yet.',
            items: upcoming,
            builder: (item) => ListTile(
              title: Text('${item['name']}'),
              subtitle: Text('${date(item['event_date'])} · ${map(item['client'])['name'] ?? 'Client'} · ${item['status']}'),
              trailing: Text('${item['guest_count'] ?? 0} guests'),
            ),
          ),
        ),
        SectionCard(
          title: 'Most Popular Services',
          child: DataList(
            empty: 'No service usage yet.',
            items: popular,
            builder: (item) => ListTile(
              title: Text('${item['name']}'),
              trailing: Text('${item['usage_count']} uses'),
            ),
          ),
        ),
      ],
    );
  }
}

class ClientsPage extends StatelessWidget {
  const ClientsPage({super.key, required this.payload, required this.api, required this.onSaved});

  final Map<String, dynamic> payload;
  final ApiClient api;
  final Future<void> Function() onSaved;

  @override
  Widget build(BuildContext context) {
    final clients = list(payload['clients']);
    return ModulePage(
      title: 'Clients',
      description: 'Register clients and track their event history.',
      actionLabel: 'Add Client',
      onAction: () => showClientForm(context, api, onSaved),
      child: SectionCard(
        title: 'Client List',
        child: DataList(
          empty: 'No clients yet.',
          items: clients,
          builder: (client) => ListTile(
            title: Text('${client['name']}'),
            subtitle: Text('${client['phone'] ?? 'No phone'} · ${client['email'] ?? 'No email'}'),
            trailing: Text('${client['events_count'] ?? 0} events'),
          ),
        ),
      ),
    );
  }
}

class EventsPage extends StatelessWidget {
  const EventsPage({super.key, required this.payload, required this.api, required this.onSaved});

  final Map<String, dynamic> payload;
  final ApiClient api;
  final Future<void> Function() onSaved;

  @override
  Widget build(BuildContext context) {
    final events = list(payload['events']);
    return ModulePage(
      title: 'Events',
      description: 'Manage dates, venues, guest counts, and statuses.',
      actionLabel: 'Create Event',
      onAction: () => showEventForm(context, payload, api, onSaved),
      child: SectionCard(
        title: 'Events',
        child: DataList(
          empty: 'No events yet.',
          items: events,
          builder: (event) => ListTile(
            title: Text('${event['name']}'),
            subtitle: Text('${date(event['event_date'])} · ${map(event['client'])['name'] ?? 'Client'} · ${event['venue'] ?? 'Venue pending'}'),
            trailing: StatusPill('${event['status']}'),
          ),
        ),
      ),
    );
  }
}

class QuotationsPage extends StatelessWidget {
  const QuotationsPage({super.key, required this.payload, required this.api, required this.onSaved});

  final Map<String, dynamic> payload;
  final ApiClient api;
  final Future<void> Function() onSaved;

  @override
  Widget build(BuildContext context) {
    final quotations = list(payload['quotations']);
    return ModulePage(
      title: 'Quotations',
      description: 'Bundled packages, balances, and booking confirmation.',
      actionLabel: 'New Quote',
      onAction: () => showQuotationForm(context, payload, api, onSaved),
      child: SectionCard(
        title: 'Quotations',
        child: DataList(
          empty: 'No quotations yet.',
          items: quotations,
          builder: (quote) => ListTile(
            title: Text('${quote['quotation_number']}'),
            subtitle: Text('${map(quote['event'])['name'] ?? 'Event'} · ${quote['payment_status']}'),
            trailing: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              crossAxisAlignment: CrossAxisAlignment.end,
              children: [
                Text(money(quote['total_amount']), style: const TextStyle(fontWeight: FontWeight.w700)),
                Text('Due ${money(quote['balance_due'])}', style: const TextStyle(fontSize: 12)),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

class MoneyPage extends StatelessWidget {
  const MoneyPage({super.key, required this.payload, required this.api, required this.onSaved});

  final Map<String, dynamic> payload;
  final ApiClient api;
  final Future<void> Function() onSaved;

  @override
  Widget build(BuildContext context) {
    final payments = list(payload['payments']);
    final expenses = list(payload['expenses']);
    return ModulePage(
      title: 'Money',
      description: 'Deposits, balance payments, and event expenses.',
      actionLabel: 'Record Payment',
      onAction: () => showPaymentForm(context, payload, api, onSaved),
      secondaryLabel: 'Add Expense',
      onSecondary: () => showExpenseForm(context, payload, api, onSaved),
      child: Column(
        children: [
          SectionCard(
            title: 'Payments',
            child: DataList(
              empty: 'No payments yet.',
              items: payments,
              builder: (payment) => ListTile(
                title: Text('${payment['type']} · ${money(payment['amount'])}'),
                subtitle: Text('${date(payment['paid_at'])} · ${payment['method'] ?? 'Method pending'}'),
              ),
            ),
          ),
          SectionCard(
            title: 'Expenses',
            child: DataList(
              empty: 'No expenses yet.',
              items: expenses,
              builder: (expense) => ListTile(
                title: Text('${expense['category']} · ${money(expense['amount'])}'),
                subtitle: Text('${expense['description']} · ${map(expense['event'])['name'] ?? 'Event'}'),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class ResourcesPage extends StatelessWidget {
  const ResourcesPage({super.key, required this.payload, required this.api, required this.onSaved});

  final Map<String, dynamic> payload;
  final ApiClient api;
  final Future<void> Function() onSaved;

  @override
  Widget build(BuildContext context) {
    final resources = list(payload['resources']);
    return ModulePage(
      title: 'Resources',
      description: 'PA systems, cameras, decor inventory, staff, and vehicles.',
      actionLabel: 'Book Resource',
      onAction: () => showResourceBookingForm(context, payload, api, onSaved),
      child: SectionCard(
        title: 'Resource Availability',
        child: DataList(
          empty: 'No resources yet.',
          items: resources,
          builder: (resource) => ListTile(
            title: Text('${resource['name']}'),
            subtitle: Text('${resource['type']} · ${resource['status']}'),
            trailing: Text('${resource['quantity']} units'),
          ),
        ),
      ),
    );
  }
}

class ReportsPage extends StatelessWidget {
  const ReportsPage({super.key, required this.payload});

  final Map<String, dynamic> payload;

  @override
  Widget build(BuildContext context) {
    final reports = map(payload['reports']);
    final profit = list(reports['profit_by_event']);
    final services = list(reports['service_performance']);
    return ModulePage(
      title: 'Reports',
      description: 'Profit, service performance, and event summaries.',
      child: Column(
        children: [
          SectionCard(
            title: 'Profit By Event',
            child: DataList(
              empty: 'No profit data yet.',
              items: profit,
              builder: (event) => ListTile(
                title: Text('${event['name']}'),
                subtitle: Text('${event['client']} · Revenue ${money(event['revenue'])} · Expenses ${money(event['expenses'])}'),
                trailing: Text(money(event['profit']), style: const TextStyle(fontWeight: FontWeight.w700)),
              ),
            ),
          ),
          SectionCard(
            title: 'Service Performance',
            child: DataList(
              empty: 'No service data yet.',
              items: services,
              builder: (service) => ListTile(
                title: Text('${service['name']}'),
                subtitle: Text('${service['usage_count']} uses'),
                trailing: Text(money(service['revenue'])),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class ModulePage extends StatelessWidget {
  const ModulePage({
    super.key,
    required this.title,
    required this.description,
    required this.child,
    this.actionLabel,
    this.onAction,
    this.secondaryLabel,
    this.onSecondary,
  });

  final String title;
  final String description;
  final Widget child;
  final String? actionLabel;
  final VoidCallback? onAction;
  final String? secondaryLabel;
  final VoidCallback? onSecondary;

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        PageTitle(title, description),
        if (actionLabel != null || secondaryLabel != null)
          Padding(
            padding: const EdgeInsets.only(bottom: 16),
            child: Wrap(
              spacing: 10,
              runSpacing: 10,
              children: [
                if (actionLabel != null)
                  FilledButton(onPressed: onAction, child: Text(actionLabel!)),
                if (secondaryLabel != null)
                  OutlinedButton(onPressed: onSecondary, child: Text(secondaryLabel!)),
              ],
            ),
          ),
        child,
      ],
    );
  }
}

class PageTitle extends StatelessWidget {
  const PageTitle(this.title, this.description, {super.key});
  final String title;
  final String description;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(title, style: Theme.of(context).textTheme.headlineSmall?.copyWith(fontWeight: FontWeight.w700)),
          const SizedBox(height: 4),
          Text(description, style: TextStyle(color: Colors.grey.shade600)),
        ],
      ),
    );
  }
}

class ResponsiveGrid extends StatelessWidget {
  const ResponsiveGrid({super.key, required this.children});
  final List<Widget> children;

  @override
  Widget build(BuildContext context) {
    final width = MediaQuery.sizeOf(context).width;
    final columns = width >= 1100 ? 5 : width >= 700 ? 3 : 1;
    return GridView.count(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      crossAxisCount: columns,
      crossAxisSpacing: 12,
      mainAxisSpacing: 12,
      childAspectRatio: columns == 1 ? 3.4 : 1.45,
      children: children,
    );
  }
}

class StatCard extends StatelessWidget {
  const StatCard(this.label, this.value, {super.key});
  final String label;
  final String value;

  @override
  Widget build(BuildContext context) {
    return Card(
      elevation: 0,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8), side: BorderSide(color: Colors.grey.shade200)),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Text(label, style: TextStyle(color: Colors.grey.shade600)),
            const SizedBox(height: 8),
            Text(value, style: Theme.of(context).textTheme.titleLarge?.copyWith(fontWeight: FontWeight.w800)),
          ],
        ),
      ),
    );
  }
}

class SectionCard extends StatelessWidget {
  const SectionCard({super.key, required this.title, required this.child});
  final String title;
  final Widget child;

  @override
  Widget build(BuildContext context) {
    return Card(
      elevation: 0,
      margin: const EdgeInsets.only(bottom: 16),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8), side: BorderSide(color: Colors.grey.shade200)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: const EdgeInsets.all(16),
            child: Text(title, style: Theme.of(context).textTheme.titleMedium?.copyWith(fontWeight: FontWeight.w700)),
          ),
          const Divider(height: 1),
          child,
        ],
      ),
    );
  }
}

class DataList extends StatelessWidget {
  const DataList({super.key, required this.items, required this.builder, required this.empty});
  final List<dynamic> items;
  final Widget Function(Map<String, dynamic>) builder;
  final String empty;

  @override
  Widget build(BuildContext context) {
    if (items.isEmpty) {
      return Padding(
        padding: const EdgeInsets.all(24),
        child: Center(child: Text(empty, style: TextStyle(color: Colors.grey.shade600))),
      );
    }

    return Column(
      children: [
        for (var index = 0; index < items.length; index++)
          Column(
            children: [
              builder(map(items[index])),
              if (index != items.length - 1) const Divider(height: 1),
            ],
          ),
      ],
    );
  }
}

class StatusPill extends StatelessWidget {
  const StatusPill(this.label, {super.key});
  final String label;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
      decoration: BoxDecoration(color: Colors.grey.shade100, borderRadius: BorderRadius.circular(999)),
      child: Text(label, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700)),
    );
  }
}

class ErrorState extends StatelessWidget {
  const ErrorState({super.key, required this.error, required this.onRetry});
  final String error;
  final Future<void> Function() onRetry;

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Icon(Icons.cloud_off, size: 42),
            const SizedBox(height: 12),
            const Text('Could not connect to the API', style: TextStyle(fontWeight: FontWeight.w700)),
            const SizedBox(height: 8),
            Text(error, textAlign: TextAlign.center, style: TextStyle(color: Colors.grey.shade600)),
            const SizedBox(height: 16),
            FilledButton(onPressed: onRetry, child: const Text('Retry')),
          ],
        ),
      ),
    );
  }
}

Future<void> showClientForm(BuildContext context, ApiClient api, Future<void> Function() onSaved) async {
  final name = TextEditingController();
  final phone = TextEditingController();
  final email = TextEditingController();
  await showEditSheet(
    context,
    title: 'Add Client',
    fields: [
      TextField(controller: name, decoration: const InputDecoration(labelText: 'Name')),
      TextField(controller: phone, decoration: const InputDecoration(labelText: 'Phone')),
      TextField(controller: email, decoration: const InputDecoration(labelText: 'Email')),
    ],
    onSave: () async {
      await api.post('/clients', {'name': name.text, 'phone': phone.text, 'email': email.text});
      await onSaved();
    },
  );
}

Future<void> showEventForm(BuildContext context, Map<String, dynamic> payload, ApiClient api, Future<void> Function() onSaved) async {
  final clients = list(payload['clients']);
  final name = TextEditingController();
  final venue = TextEditingController();
  final guests = TextEditingController(text: '50');
  var clientId = clients.isNotEmpty ? clients.first['id'] : null;
  var type = 'Wedding';
  var status = 'Inquiry';
  var eventDate = DateTime.now().add(const Duration(days: 14));

  await showEditSheet(
    context,
    title: 'Create Event',
    fields: [
      DropdownButtonFormField<dynamic>(
        value: clientId,
        decoration: const InputDecoration(labelText: 'Client'),
        items: [for (final client in clients) DropdownMenuItem(value: client['id'], child: Text('${client['name']}'))],
        onChanged: (value) => clientId = value,
      ),
      TextField(controller: name, decoration: const InputDecoration(labelText: 'Event Name')),
      TextField(controller: venue, decoration: const InputDecoration(labelText: 'Venue')),
      TextField(controller: guests, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Guest Count')),
      DropdownButtonFormField<String>(
        value: type,
        decoration: const InputDecoration(labelText: 'Type'),
        items: ['Wedding', 'Birthday', 'Corporate', 'Funeral', 'Graduation', 'Other'].map((value) => DropdownMenuItem(value: value, child: Text(value))).toList(),
        onChanged: (value) => type = value ?? type,
      ),
    ],
    onSave: () async {
      await api.post('/events', {
        'client_id': clientId,
        'name': name.text,
        'type': type,
        'event_date': isoDate(eventDate),
        'venue': venue.text,
        'guest_count': int.tryParse(guests.text) ?? 0,
        'status': status,
      });
      await onSaved();
    },
  );
}

Future<void> showQuotationForm(BuildContext context, Map<String, dynamic> payload, ApiClient api, Future<void> Function() onSaved) async {
  final events = list(payload['events']);
  final services = list(payload['services']);
  var eventId = events.isNotEmpty ? events.first['id'] : null;
  var service = services.isNotEmpty ? map(services.first) : <String, dynamic>{};
  final quantity = TextEditingController(text: '1');
  final unitPrice = TextEditingController(text: '${service['default_price'] ?? 0}');

  await showEditSheet(
    context,
    title: 'New Quotation',
    fields: [
      DropdownButtonFormField<dynamic>(
        value: eventId,
        decoration: const InputDecoration(labelText: 'Event'),
        items: [for (final event in events) DropdownMenuItem(value: event['id'], child: Text('${event['name']}'))],
        onChanged: (value) => eventId = value,
      ),
      DropdownButtonFormField<int>(
        value: service['id'] as int?,
        decoration: const InputDecoration(labelText: 'Service'),
        items: [for (final item in services) DropdownMenuItem(value: item['id'] as int, child: Text('${item['name']}'))],
        onChanged: (value) {
          service = map(services.firstWhere((item) => item['id'] == value));
          unitPrice.text = '${service['default_price'] ?? 0}';
        },
      ),
      TextField(controller: quantity, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Quantity')),
      TextField(controller: unitPrice, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Unit Price')),
    ],
    onSave: () async {
      await api.post('/quotations', {
        'event_id': eventId,
        'issued_at': isoDate(DateTime.now()),
        'status': 'Sent',
        'terms': '50% deposit confirms booking. Balance due before the event date.',
        'items': [
          {
            'service_id': service['id'],
            'description': service['name'],
            'quantity': double.tryParse(quantity.text) ?? 1,
            'unit_price': double.tryParse(unitPrice.text) ?? 0,
          }
        ],
      });
      await onSaved();
    },
  );
}

Future<void> showPaymentForm(BuildContext context, Map<String, dynamic> payload, ApiClient api, Future<void> Function() onSaved) async {
  final quotes = list(payload['quotations']);
  var quoteId = quotes.isNotEmpty ? quotes.first['id'] : null;
  final amount = TextEditingController();
  await showEditSheet(
    context,
    title: 'Record Payment',
    fields: [
      DropdownButtonFormField<dynamic>(
        value: quoteId,
        decoration: const InputDecoration(labelText: 'Quotation'),
        items: [for (final quote in quotes) DropdownMenuItem(value: quote['id'], child: Text('${quote['quotation_number']}'))],
        onChanged: (value) => quoteId = value,
      ),
      TextField(controller: amount, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Amount')),
    ],
    onSave: () async {
      await api.post('/payments', {
        'quotation_id': quoteId,
        'paid_at': isoDate(DateTime.now()),
        'amount': double.tryParse(amount.text) ?? 0,
        'type': 'Deposit',
        'method': 'Cash',
      });
      await onSaved();
    },
  );
}

Future<void> showExpenseForm(BuildContext context, Map<String, dynamic> payload, ApiClient api, Future<void> Function() onSaved) async {
  final events = list(payload['events']);
  var eventId = events.isNotEmpty ? events.first['id'] : null;
  final description = TextEditingController();
  final amount = TextEditingController();
  await showEditSheet(
    context,
    title: 'Add Expense',
    fields: [
      DropdownButtonFormField<dynamic>(
        value: eventId,
        decoration: const InputDecoration(labelText: 'Event'),
        items: [for (final event in events) DropdownMenuItem(value: event['id'], child: Text('${event['name']}'))],
        onChanged: (value) => eventId = value,
      ),
      TextField(controller: description, decoration: const InputDecoration(labelText: 'Description')),
      TextField(controller: amount, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Amount')),
    ],
    onSave: () async {
      await api.post('/expenses', {
        'event_id': eventId,
        'spent_at': isoDate(DateTime.now()),
        'category': 'Other',
        'description': description.text,
        'amount': double.tryParse(amount.text) ?? 0,
      });
      await onSaved();
    },
  );
}

Future<void> showResourceBookingForm(BuildContext context, Map<String, dynamic> payload, ApiClient api, Future<void> Function() onSaved) async {
  final resources = list(payload['resources']);
  final events = list(payload['events']);
  var resourceId = resources.isNotEmpty ? resources.first['id'] : null;
  var eventId = events.isNotEmpty ? events.first['id'] : null;
  final quantity = TextEditingController(text: '1');
  await showEditSheet(
    context,
    title: 'Book Resource',
    fields: [
      DropdownButtonFormField<dynamic>(
        value: resourceId,
        decoration: const InputDecoration(labelText: 'Resource'),
        items: [for (final resource in resources) DropdownMenuItem(value: resource['id'], child: Text('${resource['name']}'))],
        onChanged: (value) => resourceId = value,
      ),
      DropdownButtonFormField<dynamic>(
        value: eventId,
        decoration: const InputDecoration(labelText: 'Event'),
        items: [for (final event in events) DropdownMenuItem(value: event['id'], child: Text('${event['name']}'))],
        onChanged: (value) => eventId = value,
      ),
      TextField(controller: quantity, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Quantity')),
    ],
    onSave: () async {
      final event = map(events.firstWhere((item) => item['id'] == eventId, orElse: () => {}));
      await api.post('/resource-bookings', {
        'resource_id': resourceId,
        'event_id': eventId,
        'booking_date': event['event_date'] ?? isoDate(DateTime.now()),
        'quantity': int.tryParse(quantity.text) ?? 1,
        'status': 'Reserved',
      });
      await onSaved();
    },
  );
}

Future<void> showEditSheet(BuildContext context, {required String title, required List<Widget> fields, required Future<void> Function() onSave}) async {
  await showModalBottomSheet<void>(
    context: context,
    isScrollControlled: true,
    builder: (context) => EditSheet(title: title, fields: fields, onSave: onSave),
  );
}

class EditSheet extends StatefulWidget {
  const EditSheet({super.key, required this.title, required this.fields, required this.onSave});
  final String title;
  final List<Widget> fields;
  final Future<void> Function() onSave;

  @override
  State<EditSheet> createState() => _EditSheetState();
}

class _EditSheetState extends State<EditSheet> {
  bool saving = false;
  String? error;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: EdgeInsets.fromLTRB(16, 16, 16, MediaQuery.viewInsetsOf(context).bottom + 16),
      child: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(widget.title, style: Theme.of(context).textTheme.titleLarge?.copyWith(fontWeight: FontWeight.w800)),
            const SizedBox(height: 16),
            ...widget.fields.map((field) => Padding(padding: const EdgeInsets.only(bottom: 12), child: field)),
            if (error != null) Padding(padding: const EdgeInsets.only(bottom: 12), child: Text(error!, style: const TextStyle(color: Colors.red))),
            SizedBox(
              width: double.infinity,
              child: FilledButton(
                onPressed: saving
                    ? null
                    : () async {
                        setState(() {
                          saving = true;
                          error = null;
                        });
                        try {
                          await widget.onSave();
                          if (context.mounted) Navigator.pop(context);
                        } catch (exception) {
                          setState(() => error = exception.toString());
                        } finally {
                          if (mounted) setState(() => saving = false);
                        }
                      },
                child: Text(saving ? 'Saving...' : 'Save'),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

Map<String, dynamic> map(dynamic value) => value is Map<String, dynamic> ? value : Map<String, dynamic>.from(value as Map? ?? {});
List<dynamic> list(dynamic value) => value is List ? value : const [];
String money(dynamic value) => '\$${(num.tryParse('$value') ?? 0).toStringAsFixed(2)}';
String date(dynamic value) => '${value ?? 'Not set'}'.split('T').first;
String isoDate(DateTime value) => value.toIso8601String().split('T').first;
