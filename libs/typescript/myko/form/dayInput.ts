/**
 * The values of the browser's date fields (`<input type="date">` and `<input type="datetime-local">`)
 * and the `Date`s of the front's model. Both sides are in the person's local time zone: a harvest
 * weighed at 23:30 belongs to that evening's day, whatever UTC says. A field's value is a plain
 * string without a zone, so it is read and written here, never through `toISOString()`.
 */

function pad(value: number, width = 2): string {
  return String(value).padStart(width, '0');
}

/** `2026-03-12`, as `<input type="date">` takes and gives it. */
export function toDayInputValue(date: Date): string {
  return `${pad(date.getFullYear(), 4)}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
}

/** `2026-03-12T08:05`, as `<input type="datetime-local">` takes and gives it. */
export function toDateTimeInputValue(date: Date): string {
  return `${toDayInputValue(date)}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
}

const DAY_PATTERN = /^(\d{4})-(\d{2})-(\d{2})$/;
const DATE_TIME_PATTERN = /^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2})$/;

/**
 * The local `Date` a field's value stands for, or undefined for an empty or impossible one
 * (`2026-02-31` is not March 3rd: a form must say it is wrong, not guess).
 */
function readFields(
  year: number,
  month: number,
  day: number,
  hours: number,
  minutes: number,
): Date | undefined {
  const date = new Date(year, month - 1, day, hours, minutes);

  // `new Date(2026, 1, 31)` rolls over to March 3rd: compare to detect it. The year is set apart,
  // as `new Date(26, …)` means 1926.
  const isSameDate =
    date.getMonth() === month - 1 &&
    date.getDate() === day &&
    date.getHours() === hours &&
    date.getMinutes() === minutes;

  return isSameDate && date.getFullYear() === year ? date : undefined;
}

/** The local midnight of the day `value` (`2026-03-12`) names. */
export function fromDayInputValue(value: string): Date | undefined {
  const match = DAY_PATTERN.exec(value.trim());

  return match ? readFields(Number(match[1]), Number(match[2]), Number(match[3]), 0, 0) : undefined;
}

/** The local moment `value` (`2026-03-12T08:05`) names. */
export function fromDateTimeInputValue(value: string): Date | undefined {
  const match = DATE_TIME_PATTERN.exec(value.trim());

  return match
    ? readFields(
        Number(match[1]),
        Number(match[2]),
        Number(match[3]),
        Number(match[4]),
        Number(match[5]),
      )
    : undefined;
}
