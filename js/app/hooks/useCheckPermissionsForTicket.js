import React, {
  useEffect, useState,
} from 'react';
import PropTypes from 'prop-types';
import { getCurrentUser, ticketHeader } from '../../const';

/**
 * Хук
 * @component
 * @param {Object=} ticket
 * @return {JSX.Element}
 */

export default function useCheckPermissionsForTicket(ticket = {}) {
  // ********
  const [output, setOutput] = useState({ code: 99 });

  // ********
  const [isHead, setIsHead] = useState({});

  useEffect(() => {
    async function fetchCheckSupervisor() {
      const formData = new FormData();
      formData.append('ticket_id', ticket.id);
      formData.append('ticket_category_id', ticket.ticket_category_id);

      const response = await fetch(`https://api.ru/api/test?ver=${Math.random()}`, {
        method: 'POST',
        headers: ticketHeader('application/json'),
        body: formData,
      });

      const res = await response.json();

      if (res.status && res.status.code === 200) {
        setIsHead(res);
      }
    }

    fetchCheckSupervisor().then();
  }, [ticket]);

  // ********
  const [useCategories, setUseCategories] = useState({});

  useEffect(() => {
    async function fetchCheckUserCategories() {
      const response = await fetch(`https://api.ru/api/test?ver=${Math.random()}`, {
        method: 'GET',
        headers: ticketHeader('application/json', 'application/json'),
      });

      const res = await response.json();

      if (res.status && res.status.code === 200) {
        setUseCategories(res);
      }
    }

    fetchCheckUserCategories().then();
  }, [ticket]);

  useEffect(() => {
    if (isHead.status && isHead.status.code === 200 && useCategories.status && useCategories.status.code === 200) {
      // ********
      if (isHead.data && isHead.data.length > 0) {
        // Проверяем вляется ли текущий пользователь руководителем по текущему тикету?
        setOutput({
          userGroups: useCategories.data,
          watchTicket: true,
          editTicket: true,
          newComment: true,
          watchStatusBtn: false,
          newSubTicket: ticket.executor_by === getCurrentUser.serviceDeskId,
          is_head: true,
          code: 200,
          role: 'isHead',
        });
      } else if (ticket.created_by === getCurrentUser.serviceDeskId) {
        // Проверяем вляется ли текущий пользователь владельцем Тикета?
        setOutput({
          userGroups: useCategories.data,
          watchTicket: true,
          editTicket: ![5, 6].includes(ticket.ticket_status_id),
          newComment: ![5, 6].includes(ticket.ticket_status_id),
          watchStatusBtn: ![5, 6].includes(ticket.ticket_status_id),
          newSubTicket: false,
          is_head: false,
          code: 200,
          role: 'creator',
        });
      } else if (ticket.executor_by === getCurrentUser.serviceDeskId || (!(isHead.data && isHead.data.length > 0) && useCategories.data && useCategories.data.includes(ticket.ticket_category_id))) {
        // ********
        // ********
        setOutput({
          userGroups: useCategories.data,
          watchTicket: true,
          editTicket: ![5, 6].includes(ticket.ticket_status_id),
          newComment: ![5, 6].includes(ticket.ticket_status_id),
          watchStatusBtn: ![5, 6].includes(ticket.ticket_status_id),
          newSubTicket: ![1, 5, 6].includes(ticket.ticket_status_id),
          is_head: false,
          code: 200,
          role: 'executor',
        });
      } else {
        // ********
        setOutput({
          watchTicket: false, // ********
          editTicket: false, // ********
          newComment: false, // ********
          watchStatusBtn: false, // ********
          newSubTicket: false, // ********
          is_head: false, // ********
          code: 200,
          role: 'anon',
        });
      }
    }
  }, [isHead, useCategories, ticket]);

  return [output, setOutput];
}

useCheckPermissionsForTicket.defaultProps = {
  ticket: {},
};

useCheckPermissionsForTicket.propTypes = {
  ticket: PropTypes.shape(),
};
