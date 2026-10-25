<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Universal Domain Lexicon & Offline Semantic Knowledge Base
    |--------------------------------------------------------------------------
    |
    | Rich semantic dictionary mapping multi-language terminology (ID & EN)
    | to technical architectures, polyglot technology stacks, core database
    | entities (PostgreSQL Strict ULID), and contract specifications.
    |
    | Used by Project OS discovery, PRD synthesis, and scaffold generators
    | to provide O(1) offline semantic matching without relying solely on AI APIs.
    |
    */

    'domains' => [
        'bioinformatics_genomics' => [
            'id' => 'bioinformatics_genomics',
            'title' => [
                'id' => 'Platform Rekombinasi Genom & Bioinformatika',
                'en' => 'Genomics & Bioinformatics Pipeline Platform',
            ],
            'keywords' => [
                'crispr', 'cas9', 'genom', 'genomics', 'bioinformatika', 'bioinformatics',
                'dna sequencing', 'rna-seq', 'fasta', 'fastq', 'blast', 'ncbi', 'recombination',
                'genomic variant', 'alignment', 'proteomics', 'codon', 'mutation', 'crispr-cas9',
                'gene editing', 'molecular dynamics', 'phylogenetic', 'variant annotation',
                'vcf', 'samtools', 'gff3', 'biopython', 'rekayasa genetika', 'urutan dna'
            ],
            'recommended_stack' => [
                'primary_language' => 'Python 3.12 / Rust',
                'framework' => 'FastAPI / Nextflow / Celery',
                'database' => 'PostgreSQL 16 + pgvector (ULID keyset)',
                'message_broker' => 'Redis Streams / RabbitMQ Worker Pool',
                'frontend' => 'Next.js 15 (Edge SSR) + IGV.js (Genome Browser)',
                'paradigm' => 'polyglot_scientific_pipeline',
            ],
            'core_entities' => [
                'genomic_samples' => [
                    'columns' => ['id', 'sample_code', 'organism', 'tissue_origin', 'sequencing_platform', 'raw_file_uri', 'status'],
                    'indexes' => ['sample_code', 'status'],
                ],
                'variant_annotations' => [
                    'columns' => ['id', 'sample_id', 'chromosome', 'position', 'reference_allele', 'alternate_allele', 'pathogenicity_score'],
                    'indexes' => ['sample_id', 'chromosome', 'position'],
                ],
                'crispr_targets' => [
                    'columns' => ['id', 'gene_symbol', 'guide_rna_sequence', 'pam_site', 'off_target_score', 'on_target_efficiency'],
                    'indexes' => ['gene_symbol', 'off_target_score'],
                ],
                'pipeline_jobs' => [
                    'columns' => ['id', 'job_type', 'parameters', 'compute_node', 'cpu_cores', 'memory_gb', 'execution_status', 'output_uri'],
                    'indexes' => ['job_type', 'execution_status'],
                ],
            ],
            'key_endpoints' => [
                ['method' => 'POST', 'path' => '/api/v1/sequences/ingest', 'name' => 'Ingest Raw FASTA/FASTQ Batch'],
                ['method' => 'GET', 'path' => '/api/v1/variants/search', 'name' => 'Search Genetic Variants by Keyset Cursor'],
                ['method' => 'POST', 'path' => '/api/v1/crispr/design-targets', 'name' => 'Design Off-Target Scored Guide RNAs'],
                ['method' => 'GET', 'path' => '/api/v1/pipeline-jobs/{id}/status', 'name' => 'Stream Long-Running Compute Job Telemetry'],
            ],
        ],

        'fintech_ledger_banking' => [
            'id' => 'fintech_ledger_banking',
            'title' => [
                'id' => 'Core Banking & Double-Entry Ledger System',
                'en' => 'Core Banking & Double-Entry Ledger System',
            ],
            'keywords' => [
                'double-entry', 'ledger', 'buku besar', 'core banking', 'bank switch', 'iso 8583',
                'reconciliation', 'rekonsiliasi', 'settlement', 'escrow', 'chart of accounts', 'coa',
                'mutasi saldo', 'clearing', 'payment switch', 'multi-currency', 'pembukuan berpasangan',
                'debit kredit', 'jurnal umum', 'audit trail keuangan', 'financial compliance', 'ojk'
            ],
            'recommended_stack' => [
                'primary_language' => 'PHP 8.4+ / Go / Rust',
                'framework' => 'Laravel 13 / Gin (Strict Serializable Isolation)',
                'database' => 'PostgreSQL 16 Strict ULID (B-Tree Keyset O(1))',
                'message_broker' => 'Kafka / Redis Streams (Zero-Loss Idempotency)',
                'frontend' => 'React 19 Islands / Filament v5 Admin Cockpit',
                'paradigm' => 'strict_financial_ledger',
            ],
            'core_entities' => [
                'ledger_accounts' => [
                    'columns' => ['id', 'account_number', 'account_type', 'currency', 'current_balance', 'reserved_balance', 'status'],
                    'indexes' => ['account_number', 'account_type'],
                ],
                'journal_entries' => [
                    'columns' => ['id', 'reference_number', 'entry_type', 'idempotency_key', 'posted_at', 'narration', 'verified_by'],
                    'indexes' => ['reference_number', 'idempotency_key'],
                ],
                'transaction_postings' => [
                    'columns' => ['id', 'journal_entry_id', 'account_id', 'direction', 'amount', 'running_balance'],
                    'indexes' => ['journal_entry_id', 'account_id'],
                ],
                'settlement_batches' => [
                    'columns' => ['id', 'batch_code', 'clearing_date', 'total_debit', 'total_credit', 'settlement_status', 'reconciled_at'],
                    'indexes' => ['batch_code', 'settlement_status'],
                ],
            ],
            'key_endpoints' => [
                ['method' => 'POST', 'path' => '/api/v1/ledger/post-entry', 'name' => 'Atomic Double-Entry Posting with Idempotency'],
                ['method' => 'GET', 'path' => '/api/v1/ledger/accounts/{id}/statement', 'name' => 'Keyset Cursor Account Statement'],
                ['method' => 'POST', 'path' => '/api/v1/settlements/reconcile-batch', 'name' => 'Run Automated Multi-Gateway Reconciliation'],
            ],
        ],

        'iot_telemetry_robotics' => [
            'id' => 'iot_telemetry_robotics',
            'title' => [
                'id' => 'IoT Telemetri & Armada Robotika Terdistribusi',
                'en' => 'IoT Telemetry & Distributed Robotics Platform',
            ],
            'keywords' => [
                'iot', 'internet of things', 'mqtt', 'coap', 'telemetry', 'telemetri', 'drone',
                'armada robot', 'ros2', 'plc', 'modbus', 'can bus', 'sensor polling', 'ota firmware',
                'time-series', 'edge computing', 'geofencing', 'esp32', 'arduino', 'lorawan',
                'scada', 'gps tracking armada', 'sensor suhu getaran', 'hardware gateway'
            ],
            'recommended_stack' => [
                'primary_language' => 'Go / Rust / C++ / Node.js 22',
                'framework' => 'EMQX / Mosquitto MQTT + Actix / Gin',
                'database' => 'TimescaleDB / PostgreSQL 16 Strict ULID',
                'message_broker' => 'MQTT Broker + Redis Cluster',
                'frontend' => 'React 19 + Mapbox GL + Three.js 3D Twin HUD',
                'paradigm' => 'high_frequency_telemetry_edge',
            ],
            'core_entities' => [
                'iot_devices' => [
                    'columns' => ['id', 'device_serial', 'hardware_model', 'firmware_version', 'last_heartbeat_at', 'battery_pct', 'operational_status'],
                    'indexes' => ['device_serial', 'operational_status'],
                ],
                'telemetry_readings' => [
                    'columns' => ['id', 'device_id', 'recorded_at', 'latitude', 'longitude', 'altitude_m', 'speed_mps', 'sensor_payload_json'],
                    'indexes' => ['device_id', 'recorded_at'],
                ],
                'geofence_zones' => [
                    'columns' => ['id', 'zone_name', 'boundary_geojson', 'alert_on_exit', 'max_speed_limit'],
                    'indexes' => ['zone_name'],
                ],
                'device_commands' => [
                    'columns' => ['id', 'device_id', 'command_opcode', 'payload', 'sent_at', 'acknowledged_at', 'command_status'],
                    'indexes' => ['device_id', 'command_status'],
                ],
            ],
            'key_endpoints' => [
                ['method' => 'POST', 'path' => '/api/v1/telemetry/ingest-batch', 'name' => 'High-Throughput Binary/JSON Telemetry Ingestion'],
                ['method' => 'GET', 'path' => '/api/v1/devices/{id}/live-stream', 'name' => 'WebSocket / SSE Telemetry Stream'],
                ['method' => 'POST', 'path' => '/api/v1/devices/{id}/dispatch-command', 'name' => 'Send Queued Command to Hardware Device'],
            ],
        ],

        'ai_agent_rag_pipeline' => [
            'id' => 'ai_agent_rag_pipeline',
            'title' => [
                'id' => 'Autonomous AI Agent & RAG Knowledge Pipeline',
                'en' => 'Autonomous AI Agent & Enterprise RAG Pipeline',
            ],
            'keywords' => [
                'ai agent', 'autonomous agent', 'rag', 'retrieval-augmented generation', 'vector database',
                'embeddings', 'langchain', 'llamaindex', 'pytorch', 'huggingface', 'ollama', 'llm orchestrator',
                'fine-tuning', 'semantic search', 'qdrant', 'chromadb', 'milvus', 'deepseek', 'claude',
                'openai api', 'knowledge base', 'chunking', 'agentic workflow', 'tool calling', 'mcp server'
            ],
            'recommended_stack' => [
                'primary_language' => 'Python 3.12 / TypeScript Node.js 22',
                'framework' => 'FastAPI / LangGraph / LiteLLM',
                'database' => 'PostgreSQL 16 + pgvector (HNSW Indexing)',
                'message_broker' => 'Redis Streams / Celery Task Queue',
                'frontend' => 'React 19 / Next.js 15 Tailwind UI',
                'paradigm' => 'autonomous_agentic_pipeline',
            ],
            'core_entities' => [
                'agent_sessions' => [
                    'columns' => ['id', 'session_token', 'agent_name', 'system_prompt', 'model_tier', 'temperature', 'status'],
                    'indexes' => ['session_token', 'status'],
                ],
                'document_chunks' => [
                    'columns' => ['id', 'document_id', 'chunk_index', 'chunk_text', 'token_count', 'embedding_vector', 'metadata_json'],
                    'indexes' => ['document_id', 'chunk_index'],
                ],
                'retrieval_queries' => [
                    'columns' => ['id', 'session_id', 'user_prompt', 'retrieved_chunks_json', 'llm_completion', 'latency_ms', 'tokens_used'],
                    'indexes' => ['session_id'],
                ],
            ],
            'key_endpoints' => [
                ['method' => 'POST', 'path' => '/api/v1/rag/embed-document', 'name' => 'Chunk and Embed Knowledge Artifacts'],
                ['method' => 'POST', 'path' => '/api/v1/agents/chat-stream', 'name' => 'Stream Server-Sent Events (SSE) Agent Response'],
                ['method' => 'GET', 'path' => '/api/v1/agents/sessions/{id}/history', 'name' => 'Fetch Keyset Paginated Conversation Turns'],
            ],
        ],

        'game_engine_simulation' => [
            'id' => 'game_engine_simulation',
            'title' => [
                'id' => 'Simulasi & Game 3D Interaktif WebGL/WebGPU',
                'en' => 'Interactive 3D Simulation & WebGL/WebGPU Game Engine',
            ],
            'keywords' => [
                'game 3d', 'video game', 'flight simulator', 'pesawat', 'aircraft', 'three.js',
                'threejs', 'webgl', 'webgpu', 'babylon.js', 'godot', 'unity', 'game loop',
                'physics engine', 'shader', 'gltf', '6-dof', 'aerodynamics', 'hud telemetry',
                'altimeter', 'speedometer', 'yaw pitch roll', 'cockpit', 'canvas 3d', 'rapier'
            ],
            'recommended_stack' => [
                'primary_language' => 'TypeScript / WebGL / C++ WebAssembly',
                'framework' => 'Three.js / Rapier.js / Vite',
                'database' => 'PostgreSQL 16 Strict ULID (Leaderboard & Player Profiles)',
                'message_broker' => 'WebSocket Engine / Redis PubSub',
                'frontend' => 'HTML5 Canvas 60 FPS + Tailwind HUD Overlay',
                'paradigm' => 'client_rendered_realtime_simulation',
            ],
            'core_entities' => [
                'game_profiles' => [
                    'columns' => ['id', 'username', 'highest_altitude_m', 'top_speed_kmh', 'flight_hours', 'achievements_json'],
                    'indexes' => ['username'],
                ],
                'leaderboard_records' => [
                    'columns' => ['id', 'profile_id', 'circuit_name', 'lap_time_ms', 'checkpoints_cleared', 'recorded_at'],
                    'indexes' => ['circuit_name', 'lap_time_ms'],
                ],
            ],
            'key_endpoints' => [
                ['method' => 'GET', 'path' => '/api/v1/leaderboard/{circuit}', 'name' => 'Fetch Keyset Top Flight Records'],
                ['method' => 'POST', 'path' => '/api/v1/telemetry/submit-run', 'name' => 'Submit Cryptographically Signed Flight Run'],
            ],
        ],

        'game_3d_simulation' => [
            'id' => 'game_3d_simulation',
            'title' => [
                'id' => 'Simulasi Penerbangan & Game 3D WebGL',
                'en' => '3D Flight Simulation & WebGL Game Engine',
            ],
            'keywords' => [
                'game 3d', 'video game', 'flight simulator', 'pesawat', 'aircraft', 'three.js',
                'threejs', 'webgl', 'webgpu', 'babylon.js', '6-dof', 'aerodynamics', 'cockpit'
            ],
            'recommended_stack' => [
                'primary_language' => 'TypeScript / WebGL / C++ WebAssembly',
                'framework' => 'Three.js / Rapier.js / Vite',
                'database' => 'PostgreSQL 16 Strict ULID',
                'message_broker' => 'WebSocket Engine / Redis PubSub',
                'frontend' => 'HTML5 Canvas 60 FPS + Tailwind HUD Overlay',
                'paradigm' => 'client_rendered_realtime_simulation',
            ],
            'core_entities' => [
                'game_profiles' => [
                    'columns' => ['id', 'username', 'highest_altitude_m', 'top_speed_kmh', 'flight_hours', 'achievements_json'],
                    'indexes' => ['username'],
                ],
                'leaderboard_records' => [
                    'columns' => ['id', 'profile_id', 'circuit_name', 'lap_time_ms', 'checkpoints_cleared', 'recorded_at'],
                    'indexes' => ['circuit_name', 'lap_time_ms'],
                ],
            ],
            'key_endpoints' => [
                ['method' => 'GET', 'path' => '/api/v1/leaderboard/{circuit}', 'name' => 'Fetch Keyset Top Flight Records'],
                ['method' => 'POST', 'path' => '/api/v1/telemetry/submit-run', 'name' => 'Submit Cryptographically Signed Flight Run'],
            ],
        ],

        'api_gateway_messaging' => [
            'id' => 'api_gateway_messaging',
            'title' => [
                'id' => 'High-Throughput Messaging & WhatsApp API Gateway',
                'en' => 'High-Throughput Messaging & WhatsApp API Gateway',
            ],
            'keywords' => [
                'openwa', 'baileys', 'whatsapp gateway', 'wa gateway', 'api gateway', 'message broker',
                'bullmq', 'redis streams', 'webhook dispatcher', 'hmac', 'blast message', 'sms gateway',
                'telegram bot', 'broadcast whatsapp', 'session whatsapp', 'qr whatsapp', 'whatsapp api'
            ],
            'recommended_stack' => [
                'primary_language' => 'Node.js 22 / TypeScript',
                'framework' => 'Express / NestJS + Baileys / OpenWA Runner',
                'database' => 'PostgreSQL 16 Strict ULID',
                'message_broker' => 'Redis 7 + BullMQ Queue Engine',
                'frontend' => 'React 19 / Filament v5 Admin Cockpit',
                'paradigm' => 'asynchronous_messaging_gateway',
            ],
            'core_entities' => [
                'whatsapp_sessions' => [
                    'columns' => ['id', 'tenant_id', 'session_name', 'phone_number', 'connection_state', 'qr_code_raw', 'last_active_at'],
                    'indexes' => ['tenant_id', 'connection_state'],
                ],
                'outbound_messages' => [
                    'columns' => ['id', 'session_id', 'recipient_phone', 'message_type', 'content_payload_json', 'delivery_status', 'retry_count'],
                    'indexes' => ['session_id', 'delivery_status'],
                ],
                'webhook_subscriptions' => [
                    'columns' => ['id', 'tenant_id', 'endpoint_url', 'hmac_secret', 'event_types_json', 'is_active'],
                    'indexes' => ['tenant_id', 'is_active'],
                ],
            ],
            'key_endpoints' => [
                ['method' => 'POST', 'path' => '/api/v1/messages/send-text', 'name' => 'Dispatch Text Message to BullMQ Queue'],
                ['method' => 'POST', 'path' => '/api/v1/sessions/create-qr', 'name' => 'Initialize Headless WhatsApp Session & QR'],
                ['method' => 'GET', 'path' => '/api/v1/messages/delivery-status/{id}', 'name' => 'Query Message Delivery State'],
            ],
        ],

        'cybersecurity_siem_zerotrust' => [
            'id' => 'cybersecurity_siem_zerotrust',
            'title' => [
                'id' => 'Cybersecurity SIEM & Zero-Trust Threat Defense',
                'en' => 'Cybersecurity SIEM & Zero-Trust Threat Defense Platform',
            ],
            'keywords' => [
                'cybersecurity', 'siem', 'threat hunting', 'zero trust', 'honeypot', 'intrusion detection',
                'ids', 'ips', 'soc', 'cve', 'rce defense', 'mtls', 'oauth2', 'oidc', 'audit log forensics',
                'vulnerability scanner', 'ddos protection', 'waf', 'ip quarantine', 'rate limit token bucket'
            ],
            'recommended_stack' => [
                'primary_language' => 'Go / Rust / Python 3.12',
                'framework' => 'Gin / FastAPI + Vector Search Engine',
                'database' => 'PostgreSQL 16 Strict ULID + ClickHouse / Timescale',
                'message_broker' => 'Kafka / Redis Streams (High-Volume Ingestion)',
                'frontend' => 'React 19 Brutalist Security Operations Center (SOC)',
                'paradigm' => 'zero_trust_security_analytics',
            ],
            'core_entities' => [
                'security_events' => [
                    'columns' => ['id', 'source_ip', 'event_type', 'threat_severity', 'user_agent', 'payload_sample', 'detected_at'],
                    'indexes' => ['source_ip', 'event_type', 'threat_severity'],
                ],
                'ip_quarantines' => [
                    'columns' => ['id', 'ip_address', 'quarantine_reason', 'quarantined_until', 'is_active', 'quarantined_by'],
                    'indexes' => ['ip_address', 'is_active'],
                ],
                'access_policies' => [
                    'columns' => ['id', 'policy_name', 'enforcement_mode', 'allowed_cidr_blocks', 'required_claims_json'],
                    'indexes' => ['policy_name'],
                ],
            ],
            'key_endpoints' => [
                ['method' => 'POST', 'path' => '/api/v1/threats/ingest-event', 'name' => 'High-Speed Security Log Ingestion'],
                ['method' => 'GET', 'path' => '/api/v1/threats/active-quarantines', 'name' => 'Fetch Active Quarantined IP Blacklist'],
                ['method' => 'POST', 'path' => '/api/v1/threats/isolate-host', 'name' => 'Trigger Automated Quarantine Lock'],
            ],
        ],

        'decoupled_saas' => [
            'id' => 'decoupled_saas',
            'title' => [
                'id' => 'Decoupled SaaS & Distributed Microservices API',
                'en' => 'Decoupled SaaS & Distributed Microservices Architecture',
            ],
            'keywords' => [
                'decoupled', 'headless', 'microservices', 'next.js', 'nextjs', 'nuxt', 'fastapi',
                'nestjs', 'go gin', 'grpc', 'kafka', 'frontend terpisah', 'backend terpisah',
                'separated frontend', 'rest api backend', 'spa frontend'
            ],
            'recommended_stack' => [
                'primary_language' => 'TypeScript (Next.js 15) + PHP 8.4+ / Go (Headless API)',
                'framework' => 'Next.js 15 (Edge SSR) + Laravel 13 Headless API',
                'database' => 'PostgreSQL 16 Strict ULID',
                'message_broker' => 'Redis 7 (Distributed Cache & Queues)',
                'frontend' => 'Next.js 15 App Router + Tailwind CSS',
                'paradigm' => 'decoupled_headless_spa',
            ],
            'core_entities' => [
                'api_tenants' => [
                    'columns' => ['id', 'tenant_name', 'api_key_hash', 'subscription_plan', 'rate_limit_per_min', 'status'],
                    'indexes' => ['api_key_hash', 'status'],
                ],
                'saas_subscriptions' => [
                    'columns' => ['id', 'tenant_id', 'plan_tier', 'billing_cycle', 'starts_at', 'expires_at', 'is_active'],
                    'indexes' => ['tenant_id', 'is_active'],
                ],
            ],
            'key_endpoints' => [
                ['method' => 'POST', 'path' => '/api/v1/auth/token', 'name' => 'JWT / Sanctum Bearer Token Exchange'],
                ['method' => 'GET', 'path' => '/api/v1/tenants/me', 'name' => 'Fetch Authenticated Tenant Context'],
            ],
        ],
    ],
];
