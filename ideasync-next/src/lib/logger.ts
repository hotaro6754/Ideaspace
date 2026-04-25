type LogLevel = 'info' | 'warn' | 'error' | 'debug' | 'slop';

interface LogEntry {
  timestamp: string;
  level: LogLevel;
  module: string;
  message: string;
  data?: unknown;
}

export const logger = {
  log(level: LogLevel, module: string, message: string, data?: unknown) {
    const entry: LogEntry = {
      timestamp: new Date().toISOString(),
      level,
      module,
      message,
      data,
    };

    const colorMap: Record<LogLevel, string> = {
      info: '\x1b[32m',  // Green
      warn: '\x1b[33m',  // Yellow
      error: '\x1b[31m', // Red
      debug: '\x1b[36m', // Cyan
      slop: '\x1b[35m',  // Magenta (for ZeroSlop flags)
    };

    const reset = '\x1b[0m';
    const color = colorMap[level] || reset;

    console.log(`${color}[${entry.timestamp}] [${level.toUpperCase()}] [${module}]${reset} ${message}`, data || '');
  },

  info: (module: string, message: string, data?: unknown) => logger.log('info', module, message, data),
  warn: (module: string, message: string, data?: unknown) => logger.log('warn', module, message, data),
  error: (module: string, message: string, data?: unknown) => logger.log('error', module, message, data),
  debug: (module: string, message: string, data?: unknown) => logger.log('debug', module, message, data),
  slop: (module: string, message: string, data?: unknown) => logger.log('slop', module, message, data),
};
