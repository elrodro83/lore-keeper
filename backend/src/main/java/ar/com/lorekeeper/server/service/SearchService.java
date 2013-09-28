package ar.com.lorekeeper.server.service;

import java.util.ArrayList;
import java.util.List;

import org.springframework.stereotype.Service;

import ar.com.lorekeeper.server.bean.SearchResult;

@Service
public class SearchService {

	public List<SearchResult> search(final String query) {
		final List<SearchResult> suggestions = new ArrayList<SearchResult>();
		suggestions.add(new SearchResult("Place", 1L, "Kingdom A",
				"The fairiest kingdom."));
		suggestions.add(new SearchResult("Place", 2L, "Kingdom B",
				"The domain of evil."));
		suggestions.add(new SearchResult("Event", 3L, "Battle of All",
				"The ultimate combat between the two kingdoms."));
		return suggestions;
	}

}
