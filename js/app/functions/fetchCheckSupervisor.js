/**
 * @component
 * @param {Number=} docID
 * @param {Number=} categoryId
 * @return {JSX.Element}
 */

import { ticketHeader } from '../../const';

export default async function test(docID = 0, categoryId = 0) {
  let result = { data: [], groups: [] };

  const formData = new FormData();
  formData.append('ticket_id', docID);
  formData.append('ticket_category_id', categoryId);

  const response = await fetch(`https://api.ru/api/test?ver=${Math.random()}`, {
    method: 'POST',
    headers: ticketHeader('application/json'),
    body: formData,
  });

  const res = await response.json();

  if (res.status && res.status.code === 200) {
    result = res;
  }

  return result;
}
