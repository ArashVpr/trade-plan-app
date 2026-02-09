/**
 * Decision Tree Chatbot Data Structure
 * 
 * Each node contains:
 * - id: unique identifier
 * - message: bot's message text (supports basic markdown)
 * - options: array of clickable buttons with label and nextNodeId
 * - isTerminal: true if conversation ends here (optional)
 */

export const chatbotTree = {
  // Root/Welcome Node
  welcome: {
    id: 'welcome',
    message: `👋 **Welcome to Trade Plan App!**

I'm here to help you understand how this app analyzes your trading strategy - not just your profits and losses.

**What would you like to learn about?**`,
    options: [
      { label: '🎯 What is this app?', nextNodeId: 'what_is_app' },
      { label: '📊 How does scoring work?', nextNodeId: 'scoring_intro' },
      { label: '🎨 Zone Qualifiers explained', nextNodeId: 'zone_qualifiers_intro' },
      { label: '🧬 Understanding my trading profile', nextNodeId: 'trading_profile' },
      { label: '📝 Checklist vs Trade Entry', nextNodeId: 'checklist_vs_trade' },
      { label: '⚡ When should I trade?', nextNodeId: 'when_to_trade' },
    ],
  },

  // ========== WHAT IS THIS APP ==========
  what_is_app: {
    id: 'what_is_app',
    message: `**This is NOT a trading journal or performance tracker.**

📈 **Traditional trading journals** focus on:
- How much money you made or lost
- Monthly P&L statistics
- Account balance tracking

🎯 **Trade Plan App** focuses on:
- **Evaluating trade SETUPS** before execution
- Scoring your analysis quality (0-100)
- Finding which setups work for YOU
- Building a systematic, emotion-free strategy

Think of it as your **strategy validator** - it tells you which trade setups are worth taking based on YOUR historical success patterns.`,
    options: [
      { label: '💡 How does it help me?', nextNodeId: 'how_it_helps' },
      { label: '📊 Show me the workflow', nextNodeId: 'app_workflow' },
      { label: '🏠 Back to main menu', nextNodeId: 'welcome' },
    ],
  },

  how_it_helps: {
    id: 'how_it_helps',
    message: `**How Trade Plan App Helps You:**

✅ **Prevents emotional trading** - You know setups below 50 historically lose
✅ **Solidifies your strategy** - See which patterns consistently work
✅ **Prioritizes opportunities** - When you have multiple setups, trade the highest score
✅ **Stops revenge trading** - Data-driven decisions replace gut feelings
✅ **Reveals your edge** - Discover if you're a Zone Specialist, Bullish Expert, etc.

Over time, you'll develop conviction in YOUR strategy, not what works for others.`,
    options: [
      { label: '📊 Show me the workflow', nextNodeId: 'app_workflow' },
      { label: '🧪 Try creating a checklist', nextNodeId: 'create_checklist_cta', isLink: true },
      { label: '🏠 Back to main menu', nextNodeId: 'welcome' },
    ],
  },

  app_workflow: {
    id: 'app_workflow',
    message: `**Your Complete Workflow:**

**Step 1: Analyze a Setup** 🔍
Before placing a trade, evaluate the setup using our checklist wizard:
- Zone qualifiers (fresh zones, flip zones, etc.)
- Technical analysis (price location, trend direction)
- Fundamental confluence (optional)

**Step 2: Get a Score** 📊
Instantly receive a 0-100 score based on your customized weights.

**Step 3: Decide to Trade** 🎯
If the score is high enough (typically 65+), proceed with entering trade details.

**Step 4: Track Outcome** ✅
Record whether it was a win, loss, or breakeven.

**Step 5: Discover Patterns** 🧬
Visit your dashboard to see which setups consistently win for you.`,
    options: [
      { label: '📊 How does scoring work?', nextNodeId: 'scoring_intro' },
      { label: '📈 View my dashboard', nextNodeId: 'view_dashboard_cta', isLink: true },
      { label: '🏠 Back to main menu', nextNodeId: 'welcome' },
    ],
  },

  // ========== SCORING SYSTEM ==========
  scoring_intro: {
    id: 'scoring_intro',
    message: `**Scoring: Your Setup Quality Rating**

Every checklist gets a score from **0-100** based on:
- ✅ Zone qualifiers you identified (6 types)
- 📍 Technical analysis (location + direction)
- 📰 Fundamental confluence (if not excluded)

Each factor has a **weight** that you can customize. The more criteria you meet, the higher your score.

**Score Interpretation:**
- 🔴 **< 50** - High risk, historically loses
- 🟡 **50-65** - Moderate, proceed with caution
- 🟢 **65-80** - Good setup quality
- 💚 **80+** - Excellent, high confidence`,
    options: [
      { label: '⚙️ How are weights used?', nextNodeId: 'scoring_weights' },
      { label: '🎯 What score should I aim for?', nextNodeId: 'target_score' },
      { label: '🔧 Customize my scoring weights', nextNodeId: 'customize_weights_cta', isLink: true },
      { label: '🏠 Back to main menu', nextNodeId: 'welcome' },
    ],
  },

  scoring_weights: {
    id: 'scoring_weights',
    message: `**Customizable Scoring Weights**

Every trader has different preferences. You can adjust how much each factor contributes to your score:

**Zone Qualifiers** (6 types)
Each zone qualifier has its own weight (default varies)

**Technical Analysis**
- Location weight (Very Cheap, Cheap, etc.)
- Direction weight (Impulsion, Correction)

**Fundamental Analysis** (optional)
- Valuation, Seasonality, COT Index, etc.

**Example:**
If you're a pure technical trader, increase technical weights and decrease fundamental weights. If zones are your specialty, boost zone qualifier weights.`,
    options: [
      { label: '🎯 What score should I aim for?', nextNodeId: 'target_score' },
      { label: '🔧 Customize my weights now', nextNodeId: 'customize_weights_cta', isLink: true },
      { label: '🏠 Back to main menu', nextNodeId: 'welcome' },
    ],
  },

  target_score: {
    id: 'target_score',
    message: `**What Score Should You Trade?**

There's no universal answer - it depends on YOUR results! Here's the general framework:

📊 **Starting Out** (< 20 checklists)
- Trade scores 65+ to build data
- Avoid anything below 50
- You're still learning your edge

📈 **Building Confidence** (20-50 checklists)
- Check your dashboard for "Sweet Spot"
- Most traders find success at 70-80+
- Your win rate by score range becomes clear

🎯 **Experienced Trader** (50+ checklists)
- Your dashboard shows optimal range
- Only trade your proven sweet spot
- Ignore everything else (discipline wins)

**Pro Tip:** Many traders find their losses cluster below 60, and wins above 75. Let YOUR data guide you.`,
    options: [
      { label: '📈 View my score patterns', nextNodeId: 'view_dashboard_cta', isLink: true },
      { label: '📊 How does scoring work?', nextNodeId: 'scoring_intro' },
      { label: '🏠 Back to main menu', nextNodeId: 'welcome' },
    ],
  },

  // ========== ZONE QUALIFIERS ==========
  zone_qualifiers_intro: {
    id: 'zone_qualifiers_intro',
    message: `**Zone Qualifiers: Identifying High-Probability Zones**

Zone qualifiers are specific criteria that indicate a supply/demand zone has strong potential for a reaction. We track 6 types:

🌟 **Fresh Zone** - Price hasn't tested it before
🔁 **Flip Zone** - Polarity flip: former demand becomes supply (or vice versa)
🔄 **Original Zone** - Where the move originated from
📏 **LOL (Leaps Over Levels)** - Price made a strong impulsive move
⚖️ **Minimum 1:2 RR** - Risk-reward ratio is favorable
👥 **Big Brother** - Higher timeframe confluence

Which would you like to learn more about?`,
    options: [
      { label: '🌟 What is a Fresh Zone?', nextNodeId: 'fresh_zone' },
      { label: '🔁 What is a Flip Zone?', nextNodeId: 'flip_zone' },
      { label: '🔄 What is an Original Zone?', nextNodeId: 'original_zone' },
      { label: '📏 What is LOL?', nextNodeId: 'lol_zone' },
      { label: '⚖️ What is 1:2 RR?', nextNodeId: 'rr_zone' },
      { label: '👥 What is Big Brother?', nextNodeId: 'big_brother_zone' },
      { label: '🏠 Back to main menu', nextNodeId: 'welcome' },
    ],
  },

  fresh_zone: {
    id: 'fresh_zone',
    message: `**🌟 Fresh Zone**

A zone where price has **never tested before** - completely untouched.

**Why it matters:**
Fresh zones have the highest probability of causing a reaction because no traders have "used up" the supply or demand yet.

**Example:**
Price rallies from $100 to $150 without pullback. The $100 zone is fresh - no one has bought there yet. If price returns to $100, fresh demand likely exists.

**Scoring Impact:**
Fresh zones typically carry high weight in scoring because they're statistically more reliable.`,
    options: [
      { label: '🔁 What is a Flip Zone?', nextNodeId: 'flip_zone' },
      { label: '🎨 Back to all qualifiers', nextNodeId: 'zone_qualifiers_intro' },
      { label: '🏠 Back to main menu', nextNodeId: 'welcome' },
    ],
  },

  flip_zone: {
    id: 'flip_zone',
    message: `**🔁 Flip Zone**

A zone where the **polarity has flipped** - former demand becomes supply, or former supply becomes demand.

**Why it matters:**
Traders remember where price previously reversed. When price returns, psychological zones attract activity.

**Example:**
- Price found demand at $100 (buyers stepped in)
- Price eventually breaks below $100
- Now $100 becomes supply (former buyers exit)

**Polarity flip = Market memory in action.**`,
    options: [
      { label: '🔄 What is an Original Zone?', nextNodeId: 'original_zone' },
      { label: '🎨 Back to all qualifiers', nextNodeId: 'zone_qualifiers_intro' },
      { label: '🏠 Back to main menu', nextNodeId: 'welcome' },
    ],
  },

  original_zone: {
    id: 'original_zone',
    message: `**🔄 Original Zone**

The zone where a **major move originated** - the launch pad.

**Why it matters:**
Original zones often contain trapped traders or unfilled orders. When price returns, reactions can be explosive.

**Example:**
Price consolidates at $100 for weeks, then impulsively rallies to $150. The $100 zone is original - that's where the move began. Returning there often triggers another rally.

**Key:** Look for where significant moves started, not where they ended.`,
    options: [
      { label: '📏 What is LOL?', nextNodeId: 'lol_zone' },
      { label: '🎨 Back to all qualifiers', nextNodeId: 'zone_qualifiers_intro' },
      { label: '🏠 Back to main menu', nextNodeId: 'welcome' },
    ],
  },

  lol_zone: {
    id: 'lol_zone',
    message: `**📏 LOL (Leaps Over Levels)**

A zone created by a **strong, impulsive move** that leaped over multiple price levels.

**Why it matters:**
Impulsive moves indicate one-sided order flow. Strong hands are aggressively entering, overwhelming the other side.

**Example:**
Price slowly climbs from $100 to $110 over 2 weeks. Then in 3 hours, it rockets from $110 to $130 with massive volume. The $110 zone is LOL - it leaped over $120.

**Visual cue:** Look for long candles with minimal wicks.`,
    options: [
      { label: '⚖️ What is 1:2 RR?', nextNodeId: 'rr_zone' },
      { label: '🎨 Back to all qualifiers', nextNodeId: 'zone_qualifiers_intro' },
      { label: '🏠 Back to main menu', nextNodeId: 'welcome' },
    ],
  },

  rr_zone: {
    id: 'rr_zone',
    message: `**⚖️ Minimum 1:2 RR (Risk-Reward Ratio)**

The zone allows for a trade setup where potential **reward is at least 2x the risk**.

**Why it matters:**
Even with a 50% win rate, a 1:2 RR keeps you profitable. It's essential for long-term success.

**How it's calculated:**
- **Entry:** $100
- **Stop:** $98 (risk = $2)
- **Target:** $104+ (reward = $4+)
- **RR = 4/2 = 2.0** ✅

If your target is only $102, RR = 1.0 ❌

**Rule:** Never trade zones that force tight RR.`,
    options: [
      { label: '👥 What is Big Brother?', nextNodeId: 'big_brother_zone' },
      { label: '🎨 Back to all qualifiers', nextNodeId: 'zone_qualifiers_intro' },
      { label: '🏠 Back to main menu', nextNodeId: 'welcome' },
    ],
  },

  big_brother_zone: {
    id: 'big_brother_zone',
    message: `**👥 Big Brother Zone**

A zone that **aligns with a higher timeframe** supply or demand zone.

**Why it matters:**
Higher timeframes attract larger players with deeper pockets. Confluence between timeframes increases probability dramatically.

**Example:**
- You're trading 1H chart, identifying a demand zone at $100
- On the Daily chart, there's also a major demand zone at $100
- This is Big Brother confluence - institutional backing

**Pro Tip:** Always check 1-2 timeframes above your trading timeframe.`,
    options: [
      { label: '🌟 What is a Fresh Zone?', nextNodeId: 'fresh_zone' },
      { label: '🎨 Back to all qualifiers', nextNodeId: 'zone_qualifiers_intro' },
      { label: '🏠 Back to main menu', nextNodeId: 'welcome' },
    ],
  },

  // ========== TRADING PROFILE ==========
  trading_profile: {
    id: 'trading_profile',
    message: `**Understanding Your Trading Profile**

Your profile reveals your **analytical strengths** based on winning trades:

🎨 **Zone Specialist** - Masters supply/demand zones
📊 **Chart Reader** - Excels at technical price action
📰 **Macro Analyst** - Leverages economic fundamentals
⚖️ **Well-Rounded** - Balances multiple approaches

🎯 **Directional Bias:**
- **Bullish Expert** - Long positions are your strength
- **Bearish Expert** - Short positions dominate wins
- **Direction Agnostic** - Equal success both ways

**Where to find your profile?**
Visit your Dashboard after completing 10+ checklists with outcomes.`,
    options: [
      { label: '📈 View my dashboard', nextNodeId: 'view_dashboard_cta', isLink: true },
      { label: '🎯 How is directional bias calculated?', nextNodeId: 'directional_bias' },
      { label: '🏠 Back to main menu', nextNodeId: 'welcome' },
    ],
  },

  directional_bias: {
    id: 'directional_bias',
    message: `**How Directional Bias is Calculated**

Your bias is derived from **winning trades only**, using:

**Technical Factors:**
- Location (Very Cheap Correction → Bullish)
- Direction (Impulsion up → Bullish)

**Fundamental Factors:**
- Valuation (Overvalued → Bearish)
- COT Index (Commercial buying → Bullish)

Each factor is **weighted**, and we calculate a confidence level (0-100%).

**Example Result:**
"Bullish (67%)" = You win more when setups favor longs, with moderate confidence.

**Why it matters:**
Focus on trading WITH your bias. If you're a Bearish Expert, prioritize short setups.`,
    options: [
      { label: '📈 View my profile', nextNodeId: 'view_dashboard_cta', isLink: true },
      { label: '🧬 Back to trading profile', nextNodeId: 'trading_profile' },
      { label: '🏠 Back to main menu', nextNodeId: 'welcome' },
    ],
  },

  // ========== CHECKLIST VS TRADE ==========
  checklist_vs_trade: {
    id: 'checklist_vs_trade',
    message: `**Checklist vs Trade Entry - Key Difference**

📝 **Checklist (Always Created)**
- Evaluates a trade setup BEFORE execution
- Captures your analysis: zones, technicals, fundamentals
- Generates a score (0-100)
- Can be analysis-only - you don't have to trade it

💼 **Trade Entry (Optional)**
- Created ONLY if you decide to execute the trade
- Includes: entry price, stop, target, position size
- Tracks outcome: win, loss, breakeven
- Linked to the original checklist

**Workflow:**
1. Create checklist → See score
2. Score is 45 → Skip trade (analysis-only)
3. Score is 85 → Enter trade details, execute
4. Later, record outcome

**Why separate them?**
Not every analysis leads to a trade. This keeps your data clean.`,
    options: [
      { label: '🧪 Try creating a checklist', nextNodeId: 'create_checklist_cta', isLink: true },
      { label: '📊 Show me the workflow', nextNodeId: 'app_workflow' },
      { label: '🏠 Back to main menu', nextNodeId: 'welcome' },
    ],
  },

  // ========== WHEN TO TRADE ==========
  when_to_trade: {
    id: 'when_to_trade',
    message: `**When Should You Trade? The Golden Rules**

⚡ **Rule #1: Trust Your Score**
Only trade setups above YOUR proven threshold. For most traders, this is 65-80+.

⚡ **Rule #2: Use the Prioritization System**
Have 3 setups available?
- Setup A: Score 72
- Setup B: Score 88
- Setup C: Score 65

**Trade Setup B.** The highest score gets priority.

⚡ **Rule #3: Check Your Patterns**
Your dashboard shows which setups historically work for YOU. If "Bullish + 3 Zone Qualifiers" has an 80% win rate, prioritize those.

⚡ **Rule #4: Honor Your Bias**
If you're a Bearish Expert, be extra selective with long setups.

**Bottom Line:** Let data override emotion. Always.`,
    options: [
      { label: '🎯 What score should I aim for?', nextNodeId: 'target_score' },
      { label: '📈 View my winning patterns', nextNodeId: 'view_dashboard_cta', isLink: true },
      { label: '🏠 Back to main menu', nextNodeId: 'welcome' },
    ],
  },

  // ========== CALL-TO-ACTION NODES ==========
  create_checklist_cta: {
    id: 'create_checklist_cta',
    message: `**Ready to create your first checklist?**

Click the button below to open the Checklist Wizard. You'll walk through:
1. Select your trading symbol
2. Identify zone qualifiers
3. Analyze technicals
4. Add fundamentals (optional)
5. Review your score

Let's build your strategy database! 🚀`,
    options: [
      { label: '🧪 Open Checklist Wizard', nextNodeId: 'home', isLink: true },
      { label: '📖 Learn more first', nextNodeId: 'welcome' },
    ],
  },

  view_dashboard_cta: {
    id: 'view_dashboard_cta',
    message: `**View Your Trading Insights**

Your Dashboard shows:
- 📊 Score patterns & sweet spots
- 🎯 Top performing setups
- 🧬 Your trading profile & bias
- 📈 Weekly trend analysis
- 🏆 Symbol specializations

You'll need at least 5-10 completed checklists with outcomes to see meaningful patterns.`,
    options: [
      { label: '📈 Open Dashboard', nextNodeId: 'dashboard', isLink: true },
      { label: '🧪 Create a checklist first', nextNodeId: 'create_checklist_cta', isLink: true },
      { label: '🏠 Back to main menu', nextNodeId: 'welcome' },
    ],
  },

  customize_weights_cta: {
    id: 'customize_weights_cta',
    message: `**Customize Your Scoring Weights**

Adjust how much each factor contributes to your score:
- Zone qualifiers (6 types)
- Technical analysis (location, direction)
- Fundamental confluence

Make this scoring system reflect YOUR trading style!`,
    options: [
      { label: '🔧 Open Weight Settings', nextNodeId: 'checklist-weights.index', isLink: true },
      { label: '📖 Learn about scoring first', nextNodeId: 'scoring_intro' },
      { label: '🏠 Back to main menu', nextNodeId: 'welcome' },
    ],
  },
};
