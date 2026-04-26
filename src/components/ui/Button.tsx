"use client";

import { forwardRef } from 'react';
import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';
import { motion, HTMLMotionProps } from 'framer-motion';

function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

export interface ButtonProps extends HTMLMotionProps<"button"> {
  variant?: 'primary' | 'secondary' | 'ghost' | 'glass' | 'outline';
  size?: 'sm' | 'md' | 'lg' | 'xl';
}

const Button = forwardRef<HTMLButtonElement, ButtonProps>(
  ({ className, variant = 'primary', size = 'md', ...props }, ref) => {
    const variants = {
      primary: 'bg-lendi-blue text-white shadow-lendi hover:brightness-110 active:brightness-90',
      secondary: 'bg-white text-lendi-blue border border-border shadow-premium hover:bg-secondary',
      ghost: 'bg-transparent hover:bg-lendi-blue/5 text-lendi-blue',
      glass: 'bg-white/10 backdrop-blur-md border border-white/20 text-white hover:bg-white/20',
      outline: 'bg-transparent border-2 border-lendi-blue text-lendi-blue hover:bg-lendi-blue hover:text-white',
    };

    const sizes = {
      sm: 'h-9 px-4 text-xs font-bold rounded-lg',
      md: 'h-11 px-6 text-sm font-bold rounded-xl',
      lg: 'h-13 px-8 text-base font-extrabold rounded-2xl',
      xl: 'h-16 px-10 text-lg font-black rounded-3xl',
    };

    return (
      <motion.button
        ref={ref}
        whileHover={{ y: -2 }}
        whileTap={{ scale: 0.97 }}
        className={cn(
          'inline-flex items-center justify-center transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lendi-blue/50 disabled:pointer-events-none disabled:opacity-50 tracking-tight',
          variants[variant],
          sizes[size],
          className
        )}
        {...props}
      />
    );
  }
);
Button.displayName = 'Button';
export { Button };
